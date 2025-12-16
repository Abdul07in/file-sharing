<div class="bg-white shadow sm:rounded-lg overflow-hidden max-w-2xl mx-auto">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-100">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Safe Text Share
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Securely share passwords or secrets. Burn on read.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6">
        <form id="shareTextForm" class="space-y-6">
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                <div class="mt-1">
                    <textarea id="content" name="content" rows="8"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md p-3"
                        placeholder="Paste your secret key, password, or message here..." required></textarea>
                </div>
            </div>
            <div class="mt-6 flex flex-col sm:flex-row sm:justify-end">
                <button type="submit" id="shareBtn"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Encrypt & Share
                </button>
            </div>
        </form>

        <!-- Status Area -->
        <div id="statusArea" class="mt-4 hidden p-4 rounded-md">
            <p id="statusMessage" class="text-sm font-medium"></p>
        </div>

        <!-- Success Result -->
        <div id="successResult" class="hidden mt-6 bg-green-50 border-1 border-green-200 p-4 rounded-md">
            <h4 class="text-lg font-bold text-green-800 mb-2">Text Shared Successfully!</h4>
            <div class="mt-2 flex items-center gap-4">
                <span class="text-sm text-green-700">PIN:</span>
                <span id="pinDisplay"
                    class="text-2xl font-mono font-bold text-gray-800 bg-white px-4 py-2 rounded border border-gray-300 select-all"></span>
            </div>
            <p class="mt-4 text-xs text-green-600">Share this PIN. Text will be deleted after reading.</p>
            <button onclick="location.reload()"
                class="mt-4 text-sm text-primary-600 hover:text-primary-800 underline">Share more text</button>
        </div>
    </div>
</div>

<script>
    document.getElementById('shareTextForm').addEventListener('submit', async function (e) {
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
            // Using btoa only supports Latin1. Need complete unicode support.
            // A simple way for unicode: btoa(unescape(encodeURIComponent(str)))
            const base64Content = btoa(unescape(encodeURIComponent(content)));

            // Also Base64Helper expects standard base64 or data uri. 
            // "data:text/plain;base64,"...
            const dataUri = "data:text/plain;base64," + base64Content;

            // 2. Send JSON
            const response = await fetch('./api/share-text', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    content: dataUri
                })
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
</script>