document.addEventListener('DOMContentLoaded', () => {
    // --- Share Text Logic ---
    const shareTextForm = document.getElementById('shareTextForm');
    if (shareTextForm) {
        shareTextForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const content = document.getElementById('content').value;
            if (!content.trim()) return;

            const btn = document.getElementById('shareBtn');
            const statusArea = document.getElementById('statusArea');
            const statusMsg = document.getElementById('statusMessage');
            const form = document.getElementById('shareTextForm');
            const successResult = document.getElementById('successResult');
            const pinDisplay = document.getElementById('pinDisplay');

            // UI Updates
            btn.disabled = true;
            btn.innerHTML = 'Encrypting...';

            try {
                // 1. Base64 Encode
                const base64Content = btoa(unescape(encodeURIComponent(content)));
                const dataUri = "data:text/plain;base64," + base64Content;

                // 2. Send JSON
                const response = await fetch('./api/share-text', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ content: dataUri })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    form.classList.add('hidden');
                    successResult.classList.remove('hidden');
                    pinDisplay.textContent = result.data.pin;
                } else {
                    throw new Error(result.message || 'Share failed');
                }

            } catch (err) {
                statusArea.classList.remove('hidden');
                statusArea.className = 'mt-4 p-4 rounded-md bg-red-50 border-l-4 border-red-500';
                statusMsg.className = 'text-red-700';
                statusMsg.textContent = "Error: " + err.message;

                btn.disabled = false;
                btn.innerHTML = 'Encrypt & Share';
            }
        });
    }

    // --- View Text Logic ---
    const viewTextForm = document.getElementById('viewTextForm');
    if (viewTextForm) {
        // Expose helper functions to global scope for button onclicks if needed, 
        // or attach listeners here. The HTML currently uses onclick="copyText()" etc.
        // We should attach them via JS to remove inline onclicks if we want to be pure,
        // but for now let's just expose them or attach via IDs.

        // Attach helpers to window so onclick="copyText()" works
        window.copyText = function () {
            const text = document.getElementById('rawText').innerText;
            navigator.clipboard.writeText(text).then(() => {
                alert('Copied!');
            });
        };

        window.toggleMarkdown = function () {
            const rawView = document.getElementById('rawText');
            const mdView = document.getElementById('markdownPreview');

            if (mdView.classList.contains('hidden')) {
                const text = rawView.innerText;
                // Check if marked is available
                if (typeof marked !== 'undefined') {
                    mdView.innerHTML = marked.parse(text);
                } else {
                    mdView.innerHTML = '<p class="text-red-500">Markdown parser not loaded.</p>';
                }
                rawView.classList.add('hidden');
                mdView.classList.remove('hidden');
            } else {
                mdView.classList.add('hidden');
                rawView.classList.remove('hidden');
            }
        };

        viewTextForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const pin = document.getElementById('pin').value.trim();
            if (!pin) return;

            const btn = document.getElementById('viewBtn');
            const errorArea = document.getElementById('errorArea');
            const errorMessage = document.getElementById('errorMessage');

            btn.disabled = true;
            btn.innerHTML = 'Decrypting...';
            errorArea.classList.add('hidden');

            try {
                const response = await fetch('./api/view-text', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ pin: pin })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    // Success
                    const base64Data = result.data.content;
                    // remove info prefix if present (data:text/plain;base64,)
                    let rawBase64 = base64Data;
                    if (base64Data.includes(',')) {
                        rawBase64 = base64Data.split(',')[1];
                    }

                    // Decode
                    try {
                        const decodedText = decodeURIComponent(escape(atob(rawBase64)));

                        // Display
                        document.getElementById('viewTextForm').classList.add('hidden');
                        document.getElementById('headerText').textContent = "Decrypted content below.";
                        document.getElementById('resultContent').classList.remove('hidden');
                        document.getElementById('rawText').textContent = decodedText;

                    } catch (decodeErr) {
                        throw new Error("Failed to decode content.");
                    }

                } else {
                    throw new Error(result.message || 'Retrieval failed');
                }

            } catch (err) {
                errorArea.classList.remove('hidden');
                errorMessage.textContent = err.message;
                btn.disabled = false;
                btn.innerHTML = 'Decrypt & View';
            }
        });
    }
});
