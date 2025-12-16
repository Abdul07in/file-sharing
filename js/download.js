document.addEventListener('DOMContentLoaded', () => {
    const downloadForm = document.getElementById('downloadForm');

    if (downloadForm) {
        downloadForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const pin = document.getElementById('pin').value.trim();
            if (!pin) return;

            const btn = document.getElementById('downloadBtn');
            const statusArea = document.getElementById('statusArea');
            const statusMsg = document.getElementById('statusMessage');

            // UI Updates
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Decrypting...`;

            statusArea.classList.remove('hidden');
            statusArea.querySelector('div').className = 'bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md';
            statusMsg.className = 'text-blue-700';
            statusMsg.textContent = "Retrieving & Decrypting...";

            try {
                const response = await fetch('./api/receive', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ pin: pin })
                });

                const result = await response.json();

                if (result.status === 'success') {
                    statusMsg.className = 'text-green-700';
                    statusArea.querySelector('div').className = 'bg-green-50 border-l-4 border-green-500 p-4 rounded-md';
                    statusMsg.textContent = "Success! Download starting...";

                    // Handle Base64 Download
                    const base64Data = result.data.content;

                    // Trigger download
                    const link = document.createElement('a');
                    link.href = base64Data;
                    link.download = result.data.filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    // Clear pin after success
                    document.getElementById('pin').value = '';

                    setTimeout(() => {
                        btn.disabled = false;
                        btn.innerHTML = 'Decrypt & Download';
                        statusArea.classList.add('hidden');
                    }, 3000);

                } else {
                    throw new Error(result.message || 'Download failed');
                }

            } catch (err) {
                statusArea.querySelector('div').className = 'bg-red-50 border-l-4 border-red-500 p-4 rounded-md';
                statusMsg.className = 'text-red-700';
                statusMsg.textContent = "Error: " + err.message;

                btn.disabled = false;
                btn.innerHTML = 'Decrypt & Download';
            }
        });
    }
});
