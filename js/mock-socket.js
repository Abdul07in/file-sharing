export class MockWebSocket extends EventTarget {
    constructor(url) {
        super();
        this.url = url;
        this.readyState = 0; // CONNECTING
        this.roomKey = url.split('/').pop();
        this.pollInterval = null;
        this.lastId = 0;
        this.peerId = Math.random().toString(36).substring(7);



        setTimeout(() => {
            this.readyState = 1; // OPEN
            this.dispatchEvent(new Event('open'));
            this.startPolling();
        }, 100);
    }

    send(data) {
        // Wrap the original signaling payload with our PeerID
        const payload = JSON.stringify({
            pid: this.peerId,
            msg: data
        });

        fetch(`./api/signaling?action=publish`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                room_key: this.roomKey,
                message: JSON.parse(payload) // Parse it so DB stores it as JSON object, not stringified string
            })
        }).catch(err => console.error('[MockWebSocket] Send failed', err));
    }

    startPolling() {
        this.isPolling = true;
        this.pollNext();
    }

    async pollNext() {
        if (!this.isPolling || this.readyState !== 1) return;

        try {
            // Long Polling request (might take 20s to return)
            const res = await fetch(`./api/signaling?action=poll&room_key=${this.roomKey}&last_id=${this.lastId}`);

            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }

            const data = await res.json();

            if (data.messages && data.messages.length > 0) {
                data.messages.forEach(dbRow => {
                    this.lastId = dbRow.id;

                    const envelope = dbRow.data;

                    if (envelope.pid === this.peerId) {
                        return;
                    }

                    const event = new MessageEvent('message', {
                        data: envelope.msg
                    });
                    this.dispatchEvent(event);
                });
            }
        } catch (err) {
            console.error('[MockWebSocket] Poll failed, retrying in 2s', err);
            await new Promise(r => setTimeout(r, 2000)); // Backoff on error
        }

        // Wait 1s before next poll to avoid hammering the server
        if (this.isPolling && this.readyState === 1) {
            setTimeout(() => this.pollNext(), 1000);
        }
    }

    close() {
        this.readyState = 3; // CLOSED
        this.isPolling = false;
        this.dispatchEvent(new Event('close'));
    }
}
