<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="text-center mb-10 animate-fade-in">
        <h1
            class="text-4xl sm:text-5xl font-extrabold mb-4 bg-clip-text text-transparent bg-gradient-to-r from-primary-600 to-purple-600 dark:from-primary-400 dark:to-purple-400">
            P2P Direct Transfer
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
            Transfer files directly to another device without server storage. Fast, secure, and ephemeral.
        </p>
    </div>

    <div class="glass-card main-card overflow-hidden shadow-2xl rounded-2xl animate-slide-up">

        <!-- Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <button id="tab-send"
                class="flex-1 py-4 text-center font-semibold text-primary-600 border-b-2 border-primary-600 transition-colors bg-gray-50/50 dark:bg-gray-800/30">
                <i class="fas fa-paper-plane mr-2"></i> Send File
            </button>
            <button id="tab-receive"
                class="flex-1 py-4 text-center font-semibold text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors">
                <i class="fas fa-download mr-2"></i> Receive File
            </button>
        </div>

        <div class="p-8">
            <!-- SEND SECTION -->
            <div id="section-send" class="block">
                <div class="flex flex-col items-center justify-center space-y-6">

                    <!-- File Selection -->
                    <div class="w-full">
                        <label for="p2p-file-input"
                            class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-2xl cursor-pointer bg-gray-50 dark:bg-gray-800/50 border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-300 group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <div
                                    class="w-16 h-16 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-primary-500"></i>
                                </div>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                        class="font-semibold text-gray-900 dark:text-white">Click to select</span> or
                                    drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Any file size (Direct Transfer)</p>
                            </div>
                            <input id="p2p-file-input" type="file" class="hidden" />
                        </label>
                    </div>

                    <!-- Selected File Info -->
                    <div id="selected-file-info"
                        class="hidden w-full bg-primary-50 dark:bg-primary-900/20 rounded-xl p-4 border border-primary-100 dark:border-primary-800">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-file-alt text-primary-500 text-xl"></i>
                            <div class="flex-1 min-w-0">
                                <p id="file-name" class="font-semibold text-gray-900 dark:text-white truncate">
                                    filename.ext</p>
                                <p id="file-size" class="text-xs text-gray-500 dark:text-gray-400">0 MB</p>
                            </div>
                            <button id="change-file-btn"
                                class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">Change</button>
                        </div>
                    </div>

                    <!-- PIN Display (After Sending) -->
                    <div id="sender-pin-container" class="hidden flex flex-col items-center w-full animate-scale-in">
                        <p class="text-gray-600 dark:text-gray-300 mb-2">Share this PIN with the receiver:</p>
                        <div class="flex items-center gap-3 mb-4">
                            <div id="generated-pin"
                                class="text-5xl font-mono font-bold tracking-wider text-primary-600 dark:text-primary-400 bg-white dark:bg-gray-900 px-6 py-3 rounded-xl shadow-inner border border-gray-200 dark:border-gray-700 select-all">
                                ------
                            </div>
                            <button id="copy-pin-btn"
                                class="p-3 bg-gray-100 dark:bg-gray-800 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                <i class="fas fa-copy text-gray-600 dark:text-gray-400"></i>
                            </button>
                        </div>
                        <div
                            class="flex items-center text-sm text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-3 py-1.5 rounded-lg">
                            <i class="fas fa-info-circle mr-2"></i> Keep this page open while transferring.
                        </div>
                    </div>

                </div>
            </div>

            <!-- RECEIVE SECTION -->
            <div id="section-receive" class="hidden">
                <div class="flex flex-col items-center justify-center space-y-6 max-w-md mx-auto">

                    <div class="w-full">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Enter PIN
                            Code</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                            <input type="text" id="receive-pin-input"
                                class="pl-10 block w-full text-center text-2xl font-mono tracking-widest rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 py-3"
                                placeholder="000000" maxlength="6">
                        </div>
                    </div>

                    <button id="connect-btn" class="w-full btn-modern py-3.5 text-lg shadow-lg shadow-primary-500/30">
                        <i class="fas fa-bolt mr-2"></i> Connect & Download
                    </button>

                </div>
            </div>

            <!-- SHARED: STATUS & PROGRESS -->
            <div id="transfer-status-area"
                class="mt-8 hidden animate-fade-in border-t border-gray-100 dark:border-gray-700 pt-6">
                <!-- Status Log -->
                <div class="flex items-center justify-center mb-4">
                    <div id="status-spinner" class="mr-3 hidden">
                        <i class="fas fa-circle-notch fa-spin text-primary-500 text-xl"></i>
                    </div>
                    <p id="status-text" class="text-gray-700 dark:text-gray-300 font-medium">Waiting...</p>
                </div>

                <!-- Progress Bar -->
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4 overflow-hidden shadow-inner">
                    <div id="progress-bar"
                        class="bg-gradient-to-r from-primary-500 to-purple-500 h-4 rounded-full transition-all duration-300 w-0 relative">
                        <div class="absolute inset-0 bg-white/20 animate-[pulse_2s_infinite]"></div>
                    </div>
                </div>
                <div class="text-right mt-1">
                    <span id="progress-text" class="text-xs font-bold text-primary-600 dark:text-primary-400">0%</span>
                </div>
            </div>

            <!-- Download Complete Area -->
            <div id="download-area" class="hidden mt-6 text-center animate-bounce-in">
                <div
                    class="inline-block p-4 rounded-full bg-green-100 dark:bg-green-900/30 text-green-500 mb-4 ring-8 ring-green-50 dark:ring-green-900/10">
                    <i class="fas fa-check text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Transfer Complete!</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Your file has been successfully transferred.</p>
                <div id="download-actions">
                    <!-- Dynamic Download Button will be inserted here -->
                </div>
            </div>

        </div>
    </div>
</div>

<script type="module">
    import { P2PFileTransfer } from './js/p2p.js';

    // UI Elements
    const tabSend = document.getElementById('tab-send');
    const tabReceive = document.getElementById('tab-receive');
    const sectionSend = document.getElementById('section-send');
    const sectionReceive = document.getElementById('section-receive');

    // Send Elements
    const fileInput = document.getElementById('p2p-file-input');
    const fileInfo = document.getElementById('selected-file-info');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const changeFileBtn = document.getElementById('change-file-btn');
    const senderPinContainer = document.getElementById('sender-pin-container');
    const generatedPinEl = document.getElementById('generated-pin');
    const copyPinBtn = document.getElementById('copy-pin-btn');

    // Receive Elements
    const receivePinInput = document.getElementById('receive-pin-input');
    const connectBtn = document.getElementById('connect-btn');

    // Status Elements
    const statusArea = document.getElementById('transfer-status-area');
    const statusText = document.getElementById('status-text');
    const statusSpinner = document.getElementById('status-spinner');
    const progressBar = document.getElementById('progress-bar');
    const progressText = document.getElementById('progress-text');
    const downloadArea = document.getElementById('download-area');
    const downloadActions = document.getElementById('download-actions');

    let transfer = null;
    let selectedFile = null;

    // --- Tab Switching ---
    tabSend.addEventListener('click', () => {
        switchTab('send');
    });

    tabReceive.addEventListener('click', () => {
        switchTab('receive');
    });

    function switchTab(tab) {
        if (tab === 'send') {
            tabSend.classList.add('text-primary-600', 'border-b-2', 'border-primary-600', 'bg-gray-50/50', 'dark:bg-gray-800/30');
            tabSend.classList.remove('text-gray-500');
            tabReceive.classList.remove('text-primary-600', 'border-b-2', 'border-primary-600', 'bg-gray-50/50', 'dark:bg-gray-800/30');
            tabReceive.classList.add('text-gray-500');

            sectionSend.classList.remove('hidden');
            sectionReceive.classList.add('hidden');
        } else {
            tabReceive.classList.add('text-primary-600', 'border-b-2', 'border-primary-600', 'bg-gray-50/50', 'dark:bg-gray-800/30');
            tabReceive.classList.remove('text-gray-500');
            tabSend.classList.remove('text-primary-600', 'border-b-2', 'border-primary-600', 'bg-gray-50/50', 'dark:bg-gray-800/30');
            tabSend.classList.add('text-gray-500');

            sectionReceive.classList.remove('hidden');
            sectionSend.classList.add('hidden');
        }
        resetUI();
    }

    function resetUI() {
        // Reset state if needed, close existing connections
        if (transfer) {
            transfer.close();
            transfer = null;
        }
        statusArea.classList.add('hidden');
        downloadArea.classList.add('hidden');
        senderPinContainer.classList.add('hidden');
        fileInput.value = '';
        receivePinInput.value = '';
        selectedFile = null;
        fileInfo.classList.add('hidden');
        fileInput.parentElement.classList.remove('hidden');
    }

    // --- Send Logic ---
    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelection(e.target.files[0]);
        }
    });

    changeFileBtn.addEventListener('click', () => {
        fileInfo.classList.add('hidden');
        fileInput.parentElement.classList.remove('hidden');
        senderPinContainer.classList.add('hidden');
        statusArea.classList.add('hidden');
        if (transfer) transfer.close();
    });

    function handleFileSelection(file) {
        selectedFile = file;
        fileName.innerText = file.name;
        fileSize.innerText = formatBytes(file.size);

        fileInput.parentElement.classList.add('hidden');
        fileInfo.classList.remove('hidden');

        startSendingProcess();
    }

    function startSendingProcess() {
        // Generate PIN
        const pin = Math.floor(100000 + Math.random() * 900000).toString();
        generatedPinEl.innerText = pin;
        senderPinContainer.classList.remove('hidden');

        // Initialize Transfer
        initTransfer(pin, true);
    }

    copyPinBtn.addEventListener('click', () => {
        navigator.clipboard.writeText(generatedPinEl.innerText).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'PIN Copied!',
                showConfirmButton: false,
                timer: 1500
            });
        });
    });

    // --- Receive Logic ---
    connectBtn.addEventListener('click', () => {
        const pin = receivePinInput.value.trim();
        if (pin.length !== 6 || isNaN(pin)) {
            Swal.fire('Invalid PIN', 'Please enter a valid 6-digit PIN.', 'error');
            return;
        }

        initTransfer(pin, false);
    });

    // --- Transfer Core ---
    function initTransfer(pin, isInitiator) {
        if (transfer) transfer.close();

        statusArea.classList.remove('hidden');
        downloadArea.classList.add('hidden');
        statusSpinner.classList.remove('hidden');
        updateStatus(isInitiator ? 'Waiting for peer to connect...' : 'Connecting to peer...');
        updateProgress(0);

        transfer = new P2PFileTransfer(pin, isInitiator);

        // Callbacks
        transfer.onStatus = (msg) => updateStatus(msg);

        transfer.onProgress = (percent) => updateProgress(percent);

        transfer.onConnected = () => {
            statusSpinner.classList.remove('hidden'); // Keep spinning while transfer?
            updateStatus('Connected! Starting transfer...');

            if (isInitiator && selectedFile) {
                transfer.sendFile(selectedFile);
            }
        };

        transfer.onError = (err) => {
            console.error(err);
            statusSpinner.classList.add('hidden');
            updateStatus('Error: ' + err);
            Swal.fire('Error', 'Connection failed or interrupted.', 'error');
        };

        transfer.onFileReceived = (blob, metadata) => {
            statusSpinner.classList.add('hidden');
            updateStatus('File received successfully!');
            showDownload(blob, metadata);
        };

        transfer.start();
    }

    function updateStatus(msg) {
        statusText.innerText = msg;
    }

    function updateProgress(percent) {
        progressBar.style.width = percent + '%';
        progressText.innerText = percent + '%';

        if (percent === 100) {
            // progressBar.classList.add('bg-green-500');
        } else {
            // progressBar.classList.remove('bg-green-500');
        }
    }

    function showDownload(blob, metadata) {
        downloadArea.classList.remove('hidden');
        statusArea.classList.add('hidden');

        const url = URL.createObjectURL(blob);

        // Remove old buttons
        downloadActions.innerHTML = '';

        const btn = document.createElement('a');
        btn.href = url;
        btn.download = metadata.name;
        btn.className = 'btn-modern px-8 py-3 shadow-lg shadow-green-500/30 inline-flex items-center';
        btn.innerHTML = '<i class="fas fa-download mr-2"></i> Save File';

        downloadActions.appendChild(btn);

        // Auto trigger download?
        // btn.click();
    }

    function formatBytes(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }
</script>