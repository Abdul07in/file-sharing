<div class="bg-white shadow sm:rounded-lg overflow-hidden max-w-xl mx-auto">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-100">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Send a File
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Files are encrypted and stored securely.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6" id="uploadContainer">
        <form id="uploadForm" class="space-y-6">
            <div
                class="max-w-lg flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-primary-500 transition-colors group">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-primary-500 transition-colors"
                        stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 justify-center">
                        <label for="file"
                            class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                            <span id="filename-display">Upload a file</span>
                            <input id="file" name="file" type="file" class="sr-only" required>
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">
                        Max 10MB
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" id="uploadBtn"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Encrypt & Send
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

        <!-- Success Result -->
        <div id="successResult" class="hidden mt-6 bg-green-50 border-1 border-green-200 p-4 rounded-md">
            <h4 class="text-lg font-bold text-green-800 mb-2">Upload Successful!</h4>
            <p class="text-sm text-green-700">Your File PIN:</p>
            <div class="mt-2 flex items-center">
                <span id="pinDisplay"
                    class="text-2xl font-mono font-bold text-gray-800 bg-white px-4 py-2 rounded border border-gray-300 select-all"></span>
            </div>
            <p class="mt-4 text-xs text-green-600">Share this PIN with the recipient.</p>
            <button onclick="location.reload()"
                class="mt-4 text-sm text-primary-600 hover:text-primary-800 underline">Send another file</button>
        </div>

    </div>
</div>

<script>
    document.getElementById('file').addEventListener('change', function (e) {
        if (e.target.files.length > 0) {
            const fileName = e.target.files[0].name;
            const labelSpan = document.getElementById('filename-display');
            if (labelSpan) {
                labelSpan.textContent = fileName;
                labelSpan.classList.add('text-gray-900');
            }
        }
    });

    document.getElementById('uploadForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const fileInput = document.getElementById('file');
        const file = fileInput.files[0];
        if (!file) {
            alert("Please select a file first.");
            return;
        }

        const btn = document.getElementById('uploadBtn');
        const statusArea = document.getElementById('statusArea');
        const statusMsg = document.getElementById('statusMessage');
        const form = document.getElementById('uploadForm');
        const successResult = document.getElementById('successResult');
        const pinDisplay = document.getElementById('pinDisplay');

        // UI Updates
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Encrypting & Uploading...`;

        statusArea.classList.remove('hidden');
        statusArea.querySelector('div').className = 'bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md';
        statusMsg.className = 'text-blue-700';
        statusMsg.textContent = "Processing...";

        try {
            // 1. Read File as Data URL (Base64)
            const reader = new FileReader();

            reader.onload = async function () {
                try {
                    const base64String = reader.result;

                    // 2. Send JSON payload
                    const response = await fetch('./api/upload', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            filename: file.name,
                            content: base64String // This includes "data:mime;base64," prefix which helper handles
                        })
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        // Success
                        form.classList.add('hidden');
                        statusArea.classList.add('hidden');
                        successResult.classList.remove('hidden');
                        pinDisplay.textContent = result.data.pin;
                    } else {
                        throw new Error(result.message || 'Upload failed');
                    }

                } catch (err) {
                    showError(err.message);
                } finally {
                    resetBtn();
                }
            };

            reader.onerror = function () {
                showError("Failed to read file.");
                resetBtn();
            };

            reader.readAsDataURL(file);

        } catch (err) {
            showError(err.message);
            resetBtn();
        }

        function showError(msg) {
            statusArea.classList.remove('hidden');
            statusArea.querySelector('div').className = 'bg-red-50 border-l-4 border-red-500 p-4 rounded-md';
            statusMsg.className = 'text-red-700';
            statusMsg.textContent = "Error: " + msg;
        }

        function resetBtn() {
            btn.disabled = false;
            btn.innerHTML = 'Encrypt & Send';
        }
    });
</script>