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
                id: user.id,
                webrtcId: this.peerId // Map this random ID to the user for others
            }
        });

        this.localStream = null;
        this.onStreamAdded = null;
        this.onStreamRemoved = null;


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

    broadcastSignal(payload, useKeepalive = false) {
        // Add sender ID to every signal
        payload.sender = this.peerId;
        this.socket.send(JSON.stringify(payload), useKeepalive);
    }

    leave() {
        this.broadcastSignal({
            type: 'leave',
            clientID: this.awareness.clientID
        }, true);

        this.peers.forEach((peer) => {
            peer.pc.close();
        });
        this.peers.clear();
        this.socket.close();
    }

    removePeer(peerId) {
        if (this.peers.has(peerId)) {
            const peer = this.peers.get(peerId);
            peer.pc.close();
            this.peers.delete(peerId);
        }
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
                    // Collision check: We have a connection and it's not stable OR we are currently making an offer
                    const offerCollision = existingPeer.pc.signalingState !== 'stable' || existingPeer.makingOffer;

                    if (offerCollision) {
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

                if (!this.peers.has(peerId)) {
                    console.log('[SimpleWebRTC] Creating Responder for', peerId);
                    this.createPeerConnection(peerId, false);
                }

                if (this.peers.has(peerId)) {
                    console.log('[SimpleWebRTC] Handling offer from', peerId);
                    const peerOffer = this.peers.get(peerId);

                    try {
                        console.log('[SimpleWebRTC] Setting Remote Description (Offer)');
                        await peerOffer.pc.setRemoteDescription(new RTCSessionDescription(msg.payload));
                        this.processCandidateQueue(peerId);

                        const answer = await peerOffer.pc.createAnswer();
                        console.log('[SimpleWebRTC] Created Answer, setting Local Description');
                        await peerOffer.pc.setLocalDescription(answer);
                        this.broadcastSignal({ type: 'answer', target: peerId, payload: answer });
                    } catch (e) {
                        console.error('[SimpleWebRTC] Error handling offer:', e);
                    }
                }
                break;
            case 'answer':
                if (this.peers.has(peerId)) {
                    console.log('[SimpleWebRTC] Handling answer from', peerId);
                    const peerAnswer = this.peers.get(peerId);
                    // Only accept answer if we are waiting for one
                    if (peerAnswer.pc.signalingState !== 'have-local-offer') {
                        console.warn('[SimpleWebRTC] Ignoring answer in invalid state:', peerAnswer.pc.signalingState);
                        return;
                    }
                    try {
                        console.log('[SimpleWebRTC] Setting Remote Description (Answer)');
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
                    // Use standard object or RTCIceCandidate
                    const candidate = new RTCIceCandidate(msg.payload);
                    try {
                        if (peer.pc.remoteDescription && peer.pc.remoteDescription.type) {
                            await peer.pc.addIceCandidate(candidate);
                        } else {
                            peer.candidateQueue.push(candidate);
                        }
                    } catch (e) {
                        console.warn('[SimpleWebRTC] Error adding candidate, queuing for retry:', e);
                        peer.candidateQueue.push(candidate);
                    }
                }
                break;
            case 'leave':
                if (msg.clientID !== undefined) {
                    AwarenessProtocol.removeAwarenessStates(this.awareness, [msg.clientID], 'remote-leave');
                }
                if (msg.sender) {
                    this.removePeer(msg.sender);
                }
                break;
        }
    }

    async processCandidateQueue(peerId) {
        const peer = this.peers.get(peerId);
        if (peer && peer.candidateQueue.length > 0) {
            // Process sequentially to prevent races
            const queue = peer.candidateQueue;
            peer.candidateQueue = [];

            for (const c of queue) {
                try {
                    await peer.pc.addIceCandidate(c);
                } catch (e) {
                    console.warn('[SimpleWebRTC] Failed to add queued candidate:', e);
                }
            }
        }
    }

    // --- Media ---

    async enableVideo() {
        if (this.localStream) return this.localStream;

        try {
            this.localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });

            // Add tracks to all existing peers using replaceTrack on existing Transceivers
            this.peers.forEach(peer => {
                const senders = peer.pc.getSenders();

                // Map audio
                const audioTrack = this.localStream.getAudioTracks()[0];
                const audioSender = senders.find(s => s.track && s.track.kind === 'audio') ||
                    senders.find(s => !s.track && peer.pc.getTransceivers().find(t => t.sender === s && t.receiver.track.kind === 'audio'));

                // If we found a sender for audio (even if empty/null track)
                if (audioTrack) {
                    // Try to find a sender that is either already handling audio or is associated with an audio transceiver
                    // In our case, we created transceivers in order: 0=audio, 1=video.
                    const audioTransceiver = peer.pc.getTransceivers().find(t => t.receiver.track.kind === 'audio');
                    if (audioTransceiver && audioTransceiver.sender) {
                        console.log('[SimpleWebRTC] enableVideo: Replacing Audio Track on Transceiver');
                        audioTransceiver.sender.replaceTrack(audioTrack);
                        // Direction is already sendrecv
                    } else if (audioSender) {
                        console.log('[SimpleWebRTC] enableVideo: Replacing Audio Track on Sender');
                        audioSender.replaceTrack(audioTrack);
                    }
                }

                const videoTrack = this.localStream.getVideoTracks()[0];
                if (videoTrack) {
                    const videoTransceiver = peer.pc.getTransceivers().find(t => t.receiver.track.kind === 'video');
                    if (videoTransceiver && videoTransceiver.sender) {
                        console.log('[SimpleWebRTC] enableVideo: Replacing Video Track on Transceiver');
                        videoTransceiver.sender.replaceTrack(videoTrack);
                        // Direction is already sendrecv
                    }
                }
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

    disableVideo() {
        if (this.localStream) {
            // Stop all tracks
            this.localStream.getTracks().forEach(track => track.stop());

            this.peers.forEach(peer => {
                peer.pc.getSenders().forEach(sender => {
                    if (sender.track) {
                        sender.replaceTrack(null);
                        // We could set direction to 'recvonly' or 'inactive' but keeping it 'sendrecv' with null track is often safer for some implementations to avoid negotiation, 
                        // though technically we should negotiate.
                        // Let's rely on null track sending black/silence.
                    }
                });
                // Update transceivers to recvonly to stop sending? 
                // For now, simple replaceTrack(null) handles "muting".
            });

            this.localStream = null;
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

        pc.oniceconnectionstatechange = () => {
            if (pc.iceConnectionState === 'disconnected' || pc.iceConnectionState === 'failed' || pc.iceConnectionState === 'closed') {
                console.log(`[SimpleWebRTC] Connection ${pc.iceConnectionState} for ${targetPeerId}`);
                this.removePeer(targetPeerId);
            }
        };

        // Ensure consistent m-line order: Audio, Video, then Data (Data is added by createDataChannel or implicit)
        // We use addTransceiver to reserve the slots even if we don't have a stream yet.
        const audioTrack = this.localStream ? this.localStream.getAudioTracks()[0] : null;
        const videoTrack = this.localStream ? this.localStream.getVideoTracks()[0] : null;

        // ALWAYS set 'sendrecv' to avoid renegotiation when toggling tracks later.
        // If no track is provided, the browser will send silence/black frames (or null), 
        // effectively reserving the slot without needing a new Offer/Answer exchange.
        pc.addTransceiver('audio', {
            direction: 'sendrecv',
            streams: this.localStream ? [this.localStream] : []
        });
        pc.addTransceiver('video', {
            direction: 'sendrecv',
            streams: this.localStream ? [this.localStream] : []
        });
        console.log(`[SimpleWebRTC] Created Transceivers for ${targetPeerId}. Audio: ${audioTrack ? 'yes' : 'no'}, Video: ${videoTrack ? 'yes' : 'no'}`);

        // If we have tracks, we need to map the senders to them (addTransceiver might create empty sender if no stream)
        // Actually, if we pass 'streams', it initializes correctly. 
        // But let's be double sure and set the track on the sender if needed.
        if (audioTrack) {
            const sender = pc.getSenders().find(s => s.track && s.track.kind === 'audio');
            // If addTransceiver('audio', {streams...}) worked, sender.track is already set.
        }

        pc.ontrack = (event) => {
            console.log('[SimpleWebRTC] Remote Track received:', event.streams);
            let stream = event.streams[0];
            if (!stream) {
                console.log('[SimpleWebRTC] No stream in event, creating one from track');
                stream = new MediaStream();
                stream.addTrack(event.track);
            }
            if (this.onStreamAdded) {
                this.onStreamAdded(targetPeerId, stream);
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
            console.log('[SimpleWebRTC] Negotiation needed');
            if (isInitiator) {
                // Initiate renegotiation
                // We need to re-use the createOffer logic from below.
                // Refactoring createOffer logic to a method would be better.
                // For now, let's skip auto-renegotiation in this simple version unless critical.
                // Actually, `addTrack` triggers this. If we don't handle it, remote won't see video.
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
        this.peers.set(targetPeerId, peer);

        pc.onicecandidate = (event) => {
            if (event.candidate) {
                this.broadcastSignal({ type: 'candidate', target: targetPeerId, payload: event.candidate });
            }
        };

        if (isInitiator) {
            // Create Data Channel
            console.log('[SimpleWebRTC] Creating Data Channel as Initiator');
            const channel = pc.createDataChannel("yjs-sync");
            this.setupDataChannel(channel, targetPeerId);
            peer.channel = channel;

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
            });
        } else {
            // Wait for Data Channel
            pc.ondatachannel = (event) => {
                console.log('[SimpleWebRTC] Received Data Channel from remote');
                this.setupDataChannel(event.channel, targetPeerId);
                peer.channel = event.channel;
            };
        }
    }

    setupDataChannel(channel, peerId) {
        channel.onopen = () => {
            console.log(`[SimpleWebRTC] Data Channel OPEN with ${peerId}`);
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
            // console.log('[SimpleWebRTC] Message from DC', peerId);
            const msg = JSON.parse(event.data);
            this.handleDataMessage(msg);
        };

        channel.onerror = (err) => console.error('[SimpleWebRTC] Data Channel Error:', err);
        channel.onclose = () => console.log('[SimpleWebRTC] Data Channel Closed');
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
