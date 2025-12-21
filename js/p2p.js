import { MockWebSocket } from './mock-socket.js';

export class P2PFileTransfer {
    constructor(pin, isHost = false) {
        this.pin = pin;
        this.isHost = isHost;
        this.socket = null;
        this.pc = null;
        this.channel = null;

        this.onStatus = (msg) => console.log(`[P2P Status] ${msg}`);
        this.onProgress = (percent) => { };
        this.onFileReceived = (fileBlob, metadata) => { };
        this.onConnected = () => { };
        this.onError = (err) => console.error(`[P2P Error]`, err);

        this.peerId = Math.random().toString(36).substring(7);
        this.connected = false;

        // Receiving state
        this.receivedBuffer = [];
        this.receivedSize = 0;
        this.fileMetadata = null;

        console.log(`[P2P] Initialized. PIN: ${pin}, Role: ${isHost ? 'HOST (Initiator)' : 'GUEST (Listener)'}, PeerID: ${this.peerId}`);
    }

    start() {
        console.log(`[P2P] Connecting to signaling server...`);
        this.socket = new MockWebSocket(`ws://mock/${this.pin}`);
        this.socket.peerId = this.peerId;

        this.socket.addEventListener('open', () => {
            this.onStatus('Connecting to signaling server...');
            console.log(`[P2P] Signaling Open. Broadcasting JOIN.`);
            this.broadcast({ type: 'join' });
        });

        this.socket.addEventListener('message', (event) => {
            const msg = JSON.parse(event.data);
            console.log(`[P2P] Signal Received:`, msg.type, 'from', msg.sender);
            this.handleSignal(msg);
        });

        this.socket.addEventListener('close', () => {
            console.log(`[P2P] Signaling Closed.`);
        });
    }

    broadcast(signal) {
        signal.sender = this.peerId;
        console.log(`[P2P] Sending Signal:`, signal.type);
        this.socket.send(JSON.stringify(signal));
    }

    async handleSignal(msg) {
        if (!msg.sender || msg.sender === this.peerId) return;

        const peerId = msg.sender;

        switch (msg.type) {
            case 'join':
                console.log(`[P2P] Peer joined: ${peerId}`);
                // Strict Role Handling: Only HOST initiates.
                if (this.isHost) {
                    if (!this.connected) {
                        this.onStatus('Peer found. Initiating (Host)...');
                        console.log(`[P2P] I am Host. Creating Offer for ${peerId}.`);
                        this.createPeerConnection();
                    } else {
                        console.log(`[P2P] Host already connected. Ignoring JOIN.`);
                    }
                } else {
                    console.log(`[P2P] I am Guest. Waiting for Offer from Host.`);
                }
                break;
            case 'offer':
                if (this.connected) {
                    console.log(`[P2P] Received OFFER but already connected. Ignoring.`);
                    return;
                }
                this.onStatus('Received offer...');
                console.log(`[P2P] Processing OFFER.`);
                if (!this.pc) this.createPeerConnection();

                try {
                    await this.pc.setRemoteDescription(new RTCSessionDescription(msg.payload));
                    console.log(`[P2P] Remote Description SET.`);

                    const answer = await this.pc.createAnswer();
                    console.log(`[P2P] Answer Created.`);

                    await this.pc.setLocalDescription(answer);
                    console.log(`[P2P] Local Description SET (Answer).`);

                    this.broadcast({ type: 'answer', target: peerId, payload: answer });
                } catch (e) {
                    this.onError(`Error handling offer: ${e.message}`);
                }
                break;
            case 'answer':
                this.onStatus('Received answer...');
                console.log(`[P2P] Processing ANSWER.`);
                if (!this.pc) {
                    console.warn(`[P2P] Received answer but no PC exists!`);
                    return;
                }

                // FIX: Check if we are already stable. If so, ignore the duplicate answer.
                if (this.pc.signalingState === 'stable') {
                    console.warn(`[P2P] Received ANSWER but signaling state is ALREADY 'stable'. Ignoring.`);
                    return;
                }

                try {
                    await this.pc.setRemoteDescription(new RTCSessionDescription(msg.payload));
                    console.log(`[P2P] Remote Description SET (Answer).`);
                } catch (e) {
                    this.onError(`Error handling answer: ${e.message}`);
                }
                break;
            case 'candidate':
                console.log(`[P2P] Received ICE Candidate.`);
                if (this.pc) {
                    try {
                        await this.pc.addIceCandidate(new RTCIceCandidate(msg.payload));
                        console.log(`[P2P] ICE Candidate Added.`);
                    } catch (e) {
                        this.onError(`Error adding ICE candidate: ${e.message}`);
                    }
                } else {
                    console.warn(`[P2P] Received candidate but no PC exists!`);
                }
                break;
        }
    }

    createPeerConnection() {
        console.log(`[P2P] Creating RTCPeerConnection...`);
        this.pc = new RTCPeerConnection({
            iceServers: [{ urls: 'stun:stun.l.google.com:19302' }]
        });

        this.pc.oniceconnectionstatechange = () => {
            console.log(`[P2P] ICE Connection State Change: ${this.pc.iceConnectionState}`);
            if (this.pc.iceConnectionState === 'connected' || this.pc.iceConnectionState === 'completed') {
                this.connected = true;
                this.connected = true;
                this.onStatus('Encrypted P2P Connection Established!');
                // Stop signaling to prevent unnecessary polling
                if (this.socket) {
                    console.log(`[P2P] Connection established. Closing Signaling Socket.`);
                    this.socket.close();
                }
            } else if (this.pc.iceConnectionState === 'disconnected') {
                this.onStatus('Peer disconnected.');
                this.connected = false;
            } else if (this.pc.iceConnectionState === 'failed') {
                this.onError('ICE Connection Failed. Check network/firewall.');
                this.connected = false;
            }
        };

        this.pc.onicegatheringstatechange = () => {
            console.log(`[P2P] ICE Gathering State: ${this.pc.iceGatheringState}`);
        };

        this.pc.onsignalingstatechange = () => {
            console.log(`[P2P] Signaling State: ${this.pc.signalingState}`);
        };

        this.pc.onicecandidate = (event) => {
            if (event.candidate) {
                console.log(`[P2P] Generated Local ICE Candidate.`);
                this.broadcast({ type: 'candidate', payload: event.candidate });
            } else {
                console.log(`[P2P] All ICE Candidates Generated.`);
            }
        };

        if (this.isHost) {
            console.log(`[P2P] Creating Data Channel 'file-transfer'`);
            this.channel = this.pc.createDataChannel('file-transfer');
            this.setupChannel(this.channel);

            this.pc.createOffer().then(offer => {
                console.log(`[P2P] Offer Created.`);
                return this.pc.setLocalDescription(offer).then(() => {
                    console.log(`[P2P] Local Description SET (Offer).`);
                    this.broadcast({ type: 'offer', payload: offer });
                });
            }).catch(err => this.onError(`Error creating offer: ${err.message}`));
        } else {
            this.pc.ondatachannel = (event) => {
                console.log(`[P2P] Data Channel Received: ${event.channel.label}`);
                this.channel = event.channel;
                this.setupChannel(this.channel);
            };
        }
    }

    setupChannel(channel) {
        channel.binaryType = 'arraybuffer';
        channel.onopen = () => {
            console.log(`[P2P] Data Channel State: OPEN`);
            this.onStatus('Data Channel Ready.');
            this.onConnected(); // Trigger user-land Connected event only when Channel is ready
        };
        channel.onclose = () => console.log(`[P2P] Data Channel State: CLOSED`);
        channel.onerror = (err) => console.error(`[P2P] Data Channel Error:`, err);
        channel.onmessage = (event) => this.handleDataMessage(event);
    }

    sendFile(file) {
        if (!this.channel || this.channel.readyState !== 'open') {
            this.onError('Connection not ready.');
            return;
        }

        console.log(`[P2P] Sending File: ${file.name}, Size: ${file.size}`);

        const metadata = {
            type: 'meta',
            name: file.name,
            size: file.size,
            mime: file.type
        };
        this.channel.send(JSON.stringify(metadata));

        // 3. Robust Flow Control Constants
        const CHUNK_SIZE = 64 * 1024; // 64KB
        const MAX_BUFFERED = 1024 * 1024; // 1MB (Optimal safety limit)
        this.channel.bufferedAmountLowThreshold = CHUNK_SIZE;

        let offset = 0;
        const reader = new FileReader();
        let lastProgressUpdate = 0;

        reader.onerror = (err) => this.onError(`File Read Error: ${err}`);

        const readNextChunk = () => {
            // START OF FLOW CONTROL ALGORITHM
            // Check buffer pressure BEFORE reading/sending
            if (this.channel.bufferedAmount > MAX_BUFFERED) {
                // Buffer is high. Wait for it to drain.
                this.channel.addEventListener('bufferedamountlow', () => {
                    readNextChunk();
                }, { once: true });
                return;
            }

            // Safe to read next chunk
            const slice = file.slice(offset, offset + CHUNK_SIZE);
            reader.readAsArrayBuffer(slice);
        };

        reader.onload = (e) => {
            if (this.channel.readyState !== 'open') return;
            const chunk = e.target.result;

            try {
                // Attempt to send
                this.channel.send(chunk);
                offset += chunk.byteLength;

                // Throttled Progress Update
                const now = Date.now();
                if (now - lastProgressUpdate > 100 || offset >= file.size) {
                    const percent = Math.min(100, ((offset / file.size) * 100).toFixed(1));
                    this.onProgress(parseFloat(percent));
                    lastProgressUpdate = now;
                }

                if (offset < file.size) {
                    readNextChunk();
                } else {
                    this.onStatus('File sent successfully.');
                    console.log(`[P2P] File sent completely.`);
                }
            } catch (err) {
                // Safety Net: Handle "Queue Full" if it weirdly happens
                if (err.message.includes('full') || err.name === 'OperationError') {
                    // Retry: Wait for drain, then reread the SAME offset (since offset wasn't incremented)
                    this.channel.addEventListener('bufferedamountlow', () => {
                        readNextChunk();
                    }, { once: true });
                } else {
                    this.onError(`Error sending chunk: ${err.message}`);
                }
            }
        };

        readNextChunk();
    }

    handleDataMessage(event) {
        const data = event.data;

        if (typeof data === 'string') {
            try {
                const msg = JSON.parse(data);
                if (msg.type === 'meta') {
                    console.log(`[P2P] Received Metadata:`, msg);
                    this.fileMetadata = msg;
                    this.receivedBuffer = [];
                    this.receivedSize = 0;
                    this.lastProgressUpdate = 0;
                    this.onStatus(`Receiving ${msg.name}...`);
                    this.onProgress(0);
                }
            } catch (e) {
                console.warn('[P2P] Unknown string message', data);
            }
        } else {
            if (!this.fileMetadata) return;

            this.receivedBuffer.push(data);
            this.receivedSize += data.byteLength;

            // Throttle Progress Updates
            const now = Date.now();
            if (now - this.lastProgressUpdate > 50 || this.receivedSize >= this.fileMetadata.size) {
                const percent = Math.min(100, ((this.receivedSize / this.fileMetadata.size) * 100).toFixed(1));
                this.onProgress(parseFloat(percent));
                this.lastProgressUpdate = now;
            }

            if (this.receivedSize >= this.fileMetadata.size) {
                this.onStatus('File received.');
                console.log(`[P2P] File received completely. Reassembling...`);

                // Reassemble Blob
                const blob = new Blob(this.receivedBuffer, { type: this.fileMetadata.mime });
                this.onFileReceived(blob, this.fileMetadata);

                // Optimized cleanup
                this.receivedBuffer = null; // Help GC
                this.fileMetadata = null; // Ready for next file
            }
        }
    }

    close() {
        if (this.pc) {
            console.log(`[P2P] Closing PC.`);
            this.pc.close();
        }
        if (this.socket) {
            console.log(`[P2P] Closing Socket.`);
            this.socket.close();
        }
    }
}
