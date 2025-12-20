import * as Y from 'yjs';
import { Awareness } from 'y-protocols/awareness';
import * as AwarenessProtocol from 'y-protocols/awareness';

export class SimpleWebRTC {
    constructor(roomKey, ydoc, user) {
        this.roomKey = roomKey;
        this.ydoc = ydoc;
        this.peers = new Map(); // peerId -> { pc, channel, isInitiator }
        this.peerId = Math.random().toString(36).substring(7);

        // Initialize Awareness
        this.awareness = new Awareness(ydoc);
        this.awareness.setLocalState({
            user: {
                name: user.name,
                color: user.color,
                id: user.id
            }
        });



        // Connect to Signaling Server
        this.socket = new WebSocket(`ws://mock/${roomKey}`);
        // Inject our specific PeerID into the socket so it knows who we are for filtering
        this.socket.peerId = this.peerId;

        this.socket.addEventListener('open', () => {

            this.broadcastSignal({ type: 'join' });
        });

        this.socket.addEventListener('message', (event) => {
            const msg = JSON.parse(event.data);
            // Serialize signal handling to avoid race conditions (e.g. detecting Glare while processing another Offer)
            this.signalChain = this.signalChain.then(() => this.handleSignal(msg)).catch(err => {
                console.error('[SimpleWebRTC] Signal handling error:', err);
            });
        });

        // Listen for local document updates and broadcast them
        this.ydoc.on('update', (update, origin) => {
            if (origin !== this) { // Only broadcast changes NOT from us (i.e. local user inputs)
                this.broadcastData({
                    type: 'sync-update',
                    data: Array.from(update) // Convert Uint8Array to Array for JSON
                });
            }
        });

        this.signalChain = Promise.resolve();

        // Listen for local awareness updates and broadcast them
        this.awareness.on('update', ({ added, updated, removed }) => {
            const changedClients = added.concat(updated).concat(removed);
            const update = AwarenessProtocol.encodeAwarenessUpdate(this.awareness, changedClients);
            this.broadcastData({
                type: 'awareness',
                data: Array.from(update)
            });
        });
    }

    // --- Signaling ---

    broadcastSignal(payload) {
        // Add sender ID to every signal
        payload.sender = this.peerId;
        this.socket.send(JSON.stringify(payload));
    }

    async handleSignal(msg) {
        if (!msg.sender || msg.sender === this.peerId) return;

        const peerId = msg.sender;
        // Politeness: Determine who yields in a collision.
        // If my ID is lexicographically smaller, I am polite (I yield).
        const isPolite = this.peerId < peerId;

        switch (msg.type) {
            case 'join':
                // Someone joined, I will initiate connection
                console.log('[SimpleWebRTC] Peer joined:', peerId);
                this.createPeerConnection(peerId, true);
                break;
            case 'offer':
                // Check if we already have a connection
                if (this.peers.has(peerId)) {
                    const existingPeer = this.peers.get(peerId);
                    // Collision check: We have a connection and it's not stable (meaning we might be making an offer too)
                    if (existingPeer.pc.signalingState !== 'stable') {
                        console.warn(`[SimpleWebRTC] Glare detected with ${peerId}. Polite: ${isPolite}`);
                        if (!isPolite) {
                            console.log('[SimpleWebRTC] Impolite - Ignoring incoming offer');
                            return;
                        }
                        // Polite: We yield. Recreate PC to accept their offer (Rollback strategy by replacement)
                        console.log('[SimpleWebRTC] Polite - Rolling back to accept offer');
                        existingPeer.pc.close();
                        this.peers.delete(peerId);
                    }
                }

                if (!this.peers.has(peerId)) this.createPeerConnection(peerId, false);
                const peerOffer = this.peers.get(peerId);

                try {
                    await peerOffer.pc.setRemoteDescription(new RTCSessionDescription(msg.payload));
                    this.processCandidateQueue(peerId);

                    const answer = await peerOffer.pc.createAnswer();
                    await peerOffer.pc.setLocalDescription(answer);
                    this.broadcastSignal({ type: 'answer', target: peerId, payload: answer });
                } catch (e) {
                    console.error('[SimpleWebRTC] Error handling offer:', e);
                }
                break;
            case 'answer':
                if (this.peers.has(peerId)) {
                    const peerAnswer = this.peers.get(peerId);
                    // Only accept answer if we are waiting for one
                    if (peerAnswer.pc.signalingState !== 'have-local-offer') {
                        console.warn('[SimpleWebRTC] Ignoring answer in invalid state:', peerAnswer.pc.signalingState);
                        return;
                    }
                    try {
                        await peerAnswer.pc.setRemoteDescription(new RTCSessionDescription(msg.payload));
                        this.processCandidateQueue(peerId);
                    } catch (e) {
                        console.error('[SimpleWebRTC] Error handling answer:', e);
                    }
                }
                break;
            case 'candidate':
                if (this.peers.has(peerId)) {
                    const peer = this.peers.get(peerId);
                    const candidate = new RTCIceCandidate(msg.payload);
                    try {
                        if (peer.pc.remoteDescription && peer.pc.remoteDescription.type) {
                            await peer.pc.addIceCandidate(candidate);
                        } else {

                            peer.candidateQueue.push(candidate);
                        }
                    } catch (e) {
                        console.error('[SimpleWebRTC] Error adding candidate:', e);
                    }
                }
                break;
        }
    }

    processCandidateQueue(peerId) {
        const peer = this.peers.get(peerId);
        if (peer && peer.candidateQueue.length > 0) {
            // console.log(`[SimpleWebRTC] Flushing ${peer.candidateQueue.length} candidates for ${peerId}`);
            peer.candidateQueue.forEach(c => peer.pc.addIceCandidate(c).catch(e => console.error(e)));
            peer.candidateQueue = [];
        }
    }

    // --- WebRTC ---

    createPeerConnection(targetPeerId, isInitiator) {
        if (this.peers.has(targetPeerId)) return;



        const pc = new RTCPeerConnection({
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' }
            ]
        });

        const peer = { pc, channel: null, candidateQueue: [] };
        this.peers.set(targetPeerId, peer);

        pc.onicecandidate = (event) => {
            if (event.candidate) {
                this.broadcastSignal({ type: 'candidate', target: targetPeerId, payload: event.candidate });
            }
        };

        if (isInitiator) {
            // Create Data Channel
            const channel = pc.createDataChannel("yjs-sync");
            this.setupDataChannel(channel, targetPeerId);
            peer.channel = channel;

            pc.createOffer().then(offer => {
                pc.setLocalDescription(offer);
                this.broadcastSignal({ type: 'offer', target: targetPeerId, payload: offer });
            });
        } else {
            // Wait for Data Channel
            pc.ondatachannel = (event) => {
                this.setupDataChannel(event.channel, targetPeerId);
                peer.channel = event.channel;
            };
        }
    }

    setupDataChannel(channel, peerId) {
        channel.onopen = () => {
            // console.log(`[SimpleWebRTC] Channel open with ${peerId}`);
            // Send initial state
            const state = Y.encodeStateAsUpdate(this.ydoc);
            channel.send(JSON.stringify({ type: 'sync-step-1', data: Array.from(state) }));

            // Send initial awareness state
            if (this.awareness.getLocalState() !== null) {
                const awarenessUpdate = AwarenessProtocol.encodeAwarenessUpdate(this.awareness, [this.awareness.clientID]);
                channel.send(JSON.stringify({ type: 'awareness', data: Array.from(awarenessUpdate) }));
            }
        };

        channel.onmessage = (event) => {
            const msg = JSON.parse(event.data);
            this.handleDataMessage(msg);
        };
    }

    // --- Data Sync ---

    broadcastData(msg) {
        const json = JSON.stringify(msg);
        this.peers.forEach(peer => {
            if (peer.channel && peer.channel.readyState === 'open') {
                peer.channel.send(json);
            }
        });
    }

    handleDataMessage(msg) {
        if (msg.type === 'sync-update' || msg.type === 'sync-step-1') {
            // Apply update to local Yjs Doc
            // Convert Array back to Uint8Array
            const update = new Uint8Array(msg.data);
            // Pass 'this' as origin to avoid echoing it back in the 'update' event
            Y.applyUpdate(this.ydoc, update, this);
        } else if (msg.type === 'awareness') {
            const update = new Uint8Array(msg.data);
            AwarenessProtocol.applyAwarenessUpdate(this.awareness, update, this);
        }
    }
}
