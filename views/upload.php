<div class="w-full max-w-2xl mx-auto animate-slide-up">
    <div class="glass-card rounded-2xl overflow-hidden">
        <!-- Header -->
        <div
            class="px-6 py-5 bg-gradient-to-r from-primary-500/10 to-purple-500/10 dark:from-primary-900/30 dark:to-purple-900/30 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-cloud-upload-alt text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Send a File</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Files are encrypted and stored securely</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6" id="uploadContainer">
            <form id="uploadForm" class="space-y-6">
                <!-- Drop Zone -->
                <div class="drop-zone-modern cursor-pointer" id="dropZone">
                    <div class="space-y-4">
                        <div
                            class="w-20 h-20 rounded-full bg-gradient-to-br from-primary-100 to-purple-100 dark:from-primary-900/50 dark:to-purple-900/50 flex items-center justify-center mx-auto transition-transform hover:scale-110">
                            <i class="fas fa-cloud-upload-alt text-4xl text-primary-500 dark:text-primary-400"></i>
                        </div>
                        <div class="text-center">
                            <label for="file" class="cursor-pointer">
                                <span
                                    class="text-lg font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300"
                                    id="filename-display">
                                    Click to upload
                                </span>
                                <span class="text-gray-500 dark:text-gray-400"> or drag and drop</span>
                                <input id="file" name="file" type="file" class="sr-only" required>
                            </label>
                            <p class="text-sm text-gray-400 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>Maximum file size: 10MB
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Upload Button -->
                <button type="submit" id="uploadBtn" class="btn-modern w-full flex items-center justify-center gap-3">
                    <i class="fas fa-lock"></i>
                    <span>Encrypt & Send</span>
                </button>
            </form>

            <!-- Progress / Status Area -->
            <div id="statusArea" class="mt-6 hidden">
                <div
                    class="glass-card rounded-xl p-5 bg-blue-50/50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="spinner"></div>
                            <span class="text-sm font-medium text-blue-700 dark:text-blue-300"
                                id="statusMessage">Preparing...</span>
                        </div>
                        <span class="text-sm font-bold text-blue-700 dark:text-blue-300" id="progressPercent">0%</span>
                    </div>
                    <div class="w-full bg-blue-100 dark:bg-blue-900/50 rounded-full h-3 overflow-hidden">
                        <div id="progressBar" class="progress-gradient h-3 transition-all duration-300 ease-out"
                            style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Success Result -->
            <div id="successResult" class="hidden mt-6 success-animate">
                <div
                    class="glass-card rounded-xl p-6 bg-green-50/80 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-4 mb-4">
                        <div
                            class="w-14 h-14 rounded-full bg-gradient-to-br from-green-400 to-emerald-500 flex items-center justify-center shadow-lg">
                            <i class="fas fa-check text-white text-2xl"></i>
                        </div>
                        <div>
                            <h4 class="text-xl font-bold text-green-800 dark:text-green-300">Upload Successful!</h4>
                            <p class="text-sm text-green-600 dark:text-green-400">Share this PIN with the recipient</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-center my-6">
                        <span id="pinDisplay"
                            class="text-4xl font-mono font-bold text-gray-800 dark:text-white bg-white dark:bg-gray-800 px-8 py-4 rounded-2xl border-2 border-green-300 dark:border-green-700 shadow-lg select-all tracking-widest"></span>
                    </div>
                    <button onclick="location.reload()" class="btn-modern w-full"
                        style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="fas fa-paper-plane mr-2"></i>Send Another File
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="./js/upload.js"></script>

<script>
    // Enhanced drop zone interactivity
    document.addEventListener('DOMContentLoaded', () => {
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('file');
        const filenameDisplay = document.getElementById('filename-display');

        if (dropZone && fileInput) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.add('dragover');
                }, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, () => {
                    dropZone.classList.remove('dragover');
                }, false);
            });

            dropZone.addEventListener('drop', (e) => {
                const dt = e.dataTransfer;
                const files = dt.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    updateFilename(files[0].name);
                }
            }, false);

            dropZone.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', (e) => {
                if (e.target.files.length > 0) {
                    updateFilename(e.target.files[0].name);
                }
            });

            function updateFilename(name) {
                filenameDisplay.innerHTML = `<i class="fas fa-file mr-2"></i>${name}`;
                filenameDisplay.classList.add('text-green-600', 'dark:text-green-400');
            }
        }
    });
</script>