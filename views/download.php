<div class="w-full max-w-2xl mx-auto animate-slide-up">
    <div class="glass-card rounded-2xl overflow-hidden">
        <!-- Header -->
        <div
            class="px-6 py-5 bg-gradient-to-r from-emerald-500/10 to-teal-500/10 dark:from-emerald-900/30 dark:to-teal-900/30 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-download text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Receive a File</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Enter your PIN to decrypt and download</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <form id="downloadForm" class="space-y-6">
                <!-- PIN Input -->
                <div>
                    <label for="pin" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-key mr-2 text-primary-500"></i>File PIN
                    </label>
                    <input type="text" name="pin" id="pin" class="pin-input w-full" placeholder="ENTER PIN" required
                        autocomplete="off" maxlength="8">
                    <p
                        class="mt-3 text-sm text-gray-500 dark:text-gray-400 text-center flex items-center justify-center gap-2">
                        <i class="fas fa-fire text-orange-500"></i>
                        <span>Files are deleted immediately after download</span>
                    </p>
                </div>

                <!-- Download Button -->
                <button type="submit" id="downloadBtn" class="btn-modern w-full flex items-center justify-center gap-3"
                    style="background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);">
                    <i class="fas fa-unlock"></i>
                    <span>Decrypt & Download</span>
                </button>
            </form>

            <!-- Progress / Status Area -->
            <div id="statusArea" class="mt-6 hidden">
                <div class="glass-card rounded-xl p-5">
                    <div class="flex items-center gap-3">
                        <div class="spinner"></div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300" id="statusMessage"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Security Info -->
    <div class="mt-6 glass-card rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shield-alt text-white"></i>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">End-to-End Encryption</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your files are encrypted using AES-256 encryption.
                    Only you and the recipient with the PIN can access them.</p>
            </div>
        </div>
    </div>
</div>

<script src="./js/download.js"></script>