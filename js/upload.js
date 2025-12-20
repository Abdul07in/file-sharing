document.addEventListener('DOMContentLoaded', () => {
    const fileInput = document.getElementById('file');
    const uploadForm = document.getElementById('uploadForm');

    if (fileInput) {
        fileInput.addEventListener('change', function (e) {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                const labelSpan = document.getElementById('filename-display');
                if (labelSpan) {
                    labelSpan.textContent = fileName;
                    labelSpan.classList.add('text-gray-900');
                }
            }
        });
    }

    if (uploadForm) {
        uploadForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const file = fileInput.files[0];
            if (!file) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No File Selected',
                    text: 'Please select a file first.'
                });
                return;
            }

            const btn = document.getElementById('uploadBtn');
            const statusArea = document.getElementById('statusArea');
            const statusMsg = document.getElementById('statusMessage');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const successResult = document.getElementById('successResult');
            const pinDisplay = document.getElementById('pinDisplay');

            // UI Updates
            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-circle-notch fa-spin -ml-1 mr-3 text-white inline"></i> Initializing...`;

            statusArea.classList.remove('hidden');
            statusMsg.textContent = "Prepare for upload...";
            if (progressBar) progressBar.style.width = '0%';
            if (progressPercent) progressPercent.textContent = '0%';

            // Ensure correct style for status area (blue)
            statusArea.querySelector('div').className = 'bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md';
            if (progressBar) progressBar.parentElement.classList.remove('hidden');

            try {
                await uploadFileChunked(file);
            } catch (err) {
                showError(err.message);
                resetBtn();
            }

            async function uploadFileChunked(file) {
                const CHUNK_SIZE = 1024 * 1024; // 1MB
                const totalChunks = Math.ceil(file.size / CHUNK_SIZE);

                // 1. Init
                const initResponse = await postData({ action: 'init' });
                if (initResponse.status !== 'success') throw new Error(initResponse.message || 'Init failed');
                const uploadId = initResponse.data.upload_id;

                // 2. Chunk Loop
                for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                    const start = chunkIndex * CHUNK_SIZE;
                    const end = Math.min(start + CHUNK_SIZE, file.size);
                    const blob = file.slice(start, end);

                    // Update UI BEFORE uploading
                    const percent = Math.round((chunkIndex / totalChunks) * 100);
                    statusMsg.textContent = `Uploading...`;
                    if (progressPercent) progressPercent.textContent = `${percent}%`;
                    if (progressBar) progressBar.style.width = `${percent}%`;

                    // Convert Blob to Base64
                    const base64Data = await new Promise((resolve, reject) => {
                        const reader = new FileReader();
                        reader.onload = () => {
                            const result = reader.result;
                            const base64 = result.split(',')[1];
                            resolve(base64);
                        };
                        reader.onerror = reject;
                        reader.readAsDataURL(blob);
                    });

                    // Send Chunk
                    const chunkResponse = await postData({
                        action: 'chunk',
                        upload_id: uploadId,
                        chunk_data: base64Data
                    });

                    if (chunkResponse.status !== 'success') throw new Error(chunkResponse.message || 'Chunk failed');
                }

                // 100%
                if (progressBar) progressBar.style.width = '100%';
                if (progressPercent) progressPercent.textContent = '100%';
                statusMsg.textContent = "Finalizing encryption...";

                // 3. Complete
                btn.innerHTML = `<i class="fas fa-circle-notch fa-spin -ml-1 mr-3 text-white inline"></i> Encrypting...`;

                const completeResponse = await postData({
                    action: 'complete',
                    upload_id: uploadId,
                    filename: file.name
                });

                if (completeResponse.status === 'success') {
                    // Success
                    uploadForm.classList.add('hidden');
                    statusArea.classList.add('hidden');
                    successResult.classList.remove('hidden');
                    pinDisplay.textContent = completeResponse.data.pin;
                    resetBtn();
                } else {
                    throw new Error(completeResponse.message || 'Completion failed');
                }
            }

            async function postData(data) {
                const response = await fetch('./api/upload', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                return await response.json();
            }

            function showError(msg) {
                statusArea.classList.remove('hidden');
                statusMsg.textContent = "Error: " + msg;
                // Also update parent div to red
                const container = statusArea.querySelector('div');
                if (container) {
                    container.className = 'bg-red-50 border-l-4 border-red-500 p-4 rounded-md';
                    // Find and hide/style progress bar for error
                    const pb = document.getElementById('progressBar');
                    // Hide the progress bar container in error state
                    if (pb && pb.parentElement) pb.parentElement.classList.add('hidden');
                }
            }

            function resetBtn() {
                btn.disabled = false;
                btn.innerHTML = 'Encrypt & Send';
            }
        });
    }
});
