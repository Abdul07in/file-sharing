<div class="bg-white shadow sm:rounded-lg overflow-hidden max-w-xl mx-auto">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-100">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Receive a File
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Enter your PIN to decrypt and download.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6">
        <form id="downloadForm" class="space-y-6">
            <div>
                <label for="pin" class="block text-sm font-medium text-gray-700">File PIN</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="text" name="pin" id="pin"
                        class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-4 pr-12 sm:text-lg border-gray-300 rounded-md py-3 font-mono tracking-widest text-center uppercase"
                        placeholder="ENTER PIN" required autocomplete="off">
                </div>
                <p class="mt-2 text-xs text-gray-500 text-center">
                    Files are deleted immediately after download.
                </p>
            </div>

            <div class="mt-6">
                <button type="submit" id="downloadBtn"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Decrypt & Download
                </button>
            </div>
        </form>

        <!-- Progress / Status Area -->
        <div id="statusArea" class="mt-4 hidden">
            <div class="p-4 rounded-md">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium" id="statusMessage"></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    document.getElementById('downloadForm').addEventListener('submit', async function (e) {
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
                const base64Data = result.data.content; // Expecting "data:mime;base64,..." or just base64?
                // The Helper returns just raw base64 or complete data URI depending on usage.
                // In FileController: Base64Helper::encode($decryptedContent).
                // Base64Helper::encode returns complete data URI: data:mime;base64,.....

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
</script>