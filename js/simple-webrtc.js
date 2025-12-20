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

<<<<<<< HEAD
        this.localStream = null;
        this.onStreamAdded = null;
        this.onStreamRemoved = null;

=======
>>>>>>> origin/main


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
<<<<<<< HEAD
                    // Collision check: We have a connection and it's not stable OR we are currently making an offer
                    const offerCollision = existingPeer.pc.signalingState !== 'stable' || existingPeer.makingOffer;

                    if (offerCollision) {
=======
                    // Collision check: We have a connection and it's not stable (meaning we might be making an offer too)
                    if (existingPeer.pc.signalingState !== 'stable') {
>>>>>>> origin/main
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

<<<<<<< HEAD
    // --- Media ---

    async enableVideo() {
        if (this.localStream) return this.localStream;

        try {
            this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });

            // Add tracks to all existing peers
            this.peers.forEach(peer => {
                this.localStream.getTracks().forEach(track => {
                    peer.pc.addTrack(track, this.localStream);
                });
                // If we are polite (not initiator) and connection is stable, we might need to renegotiate.
                // But simplified: Just adding track usually triggers 'negotiationneeded'.
                // However, for manual signaling, we might need to kick it.
                // For this simple version, we assume video is enabled BEFORE peers connect OR we handle renegotiation.
                // SimpleWebRTC currently doesn't handle renegotiation perfectly (glare handling is basic).
                // Let's rely on 'negotiationneeded' if we want to be robust, OR specific "add-track" logic.

                // Trigger renegotiation if needed
                // peer.pc.onnegotiationneeded should fire, but we need to hook it up?
                // Currently code doesn't have onnegotiationneeded. Let's add it in createPeerConnection.
            });

            return this.localStream;
        } catch (e) {
            console.error('[SimpleWebRTC] Error getting user media:', e);
            throw e;
        }
    }

    toggleAudio(enabled) {
        if (this.localStream) {
            this.localStream.getAudioTracks().forEach(t => t.enabled = enabled);
        }
    }

    toggleVideo(enabled) {
        if (this.localStream) {
            this.localStream.getVideoTracks().forEach(t => t.enabled = enabled);
        }
    }

    toggleVideo(enabled) {
        if (this.localStream) {
            this.localStream.getVideoTracks().forEach(t => t.enabled = enabled);
        }
    }

    disableVideo() {
        if (this.localStream) {
            // Stop all tracks
            this.localStream.getTracks().forEach(track => {
                track.stop();
                // Remove from all peers
                this.peers.forEach(peer => {
                    // Modern removeTrack requires sender, but simple addTrack returns sender?
                    // Actually peer.pc.getSenders() is better.
                    const sender = peer.pc.getSenders().find(s => s.track === track);
                    if (sender) {
                        peer.pc.removeTrack(sender);
                    }
                });
            });
            this.localStream = null;
        }
    }

=======
>>>>>>> origin/main
    // --- WebRTC ---

    createPeerConnection(targetPeerId, isInitiator) {
        if (this.peers.has(targetPeerId)) return;



        const pc = new RTCPeerConnection({
            iceServers: [
                { urls: 'stun:stun.l.google.com:19302' }
            ]
        });

<<<<<<< HEAD
        // Add local stream tracks if available
        if (this.localStream) {
            this.localStream.getTracks().forEach(track => {
                pc.addTrack(track, this.localStream);
            });
        }

        pc.ontrack = (event) => {
            console.log('[SimpleWebRTC] Remote Track received:', event.streams);
            if (this.onStreamAdded) {
                this.onStreamAdded(targetPeerId, event.streams[0]);
            }
        };

        pc.onnegotiationneeded = () => {
            // Only the stable/initiator should maybe start? 
            // Or both? The 'perfect negotiation' pattern handles this universally.
            // Our manual simple pattern: if we are making offer or already stable, we can try.
            // But to avoid complexity in this file-sharing app, 
            // we will let `enableVideo` be called primarily upon joining or assuming users join after enabling.
            // IF we need dynamic add/remove, we need a robust `makingOffer` check here.

            // Minimal implementation:
            if (peer.makingOffer) return;
            // Only initiator negotiates? Or anyone?
            // Let's stick to the join flow mostly. 
            // But if we add track later:
            if (isInitiator) {
                // Initiate renegotiation
                // We need to re-use the createOffer logic from below.
                // Refactoring createOffer logic to a method would be better.
                // For now, let's skip auto-renegotiation in this simple version unless critical.
                // Actually, `addTrack` triggers this. If we don't handle it, remote won't see video.
                console.log('[SimpleWebRTC] Negotiation needed');
                peer.makingOffer = true;
                pc.createOffer().then(offer => {
                    if (pc.signalingState === 'closed') return;
                    return pc.setLocalDescription(offer).then(() => {
                        this.broadcastSignal({ type: 'offer', target: targetPeerId, payload: offer });
                    });
                }).catch(e => console.error(e)).finally(() => peer.makingOffer = false);
            }
        };

        const peer = { pc, channel: null, candidateQueue: [], makingOffer: false };
=======
        const peer = { pc, channel: null, candidateQueue: [] };
>>>>>>> origin/main
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

<<<<<<< HEAD
            peer.makingOffer = true;
            pc.createOffer().then(offer => {
                // If the PC was closed (e.g. by rollback) or state changed, don't proceed without caution
                if (pc.signalingState === 'closed') return;
                return pc.setLocalDescription(offer).then(() => {
                    this.broadcastSignal({ type: 'offer', target: targetPeerId, payload: offer });
                });
            }).catch(e => {
                console.error('[SimpleWebRTC] Error creating offer:', e);
            }).finally(() => {
                peer.makingOffer = false;
=======
            pc.createOffer().then(offer => {
                pc.setLocalDescription(offer);
                this.broadcastSignal({ type: 'offer', target: targetPeerId, payload: offer });
>>>>>>> origin/main
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
