<div class="w-full max-w-3xl mx-auto animate-slide-up">
    <div class="glass-card rounded-2xl overflow-hidden">
        <!-- Header -->
        <div
            class="px-6 py-5 bg-gradient-to-r from-purple-500/10 to-pink-500/10 dark:from-purple-900/30 dark:to-pink-900/30 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-comment-dots text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Safe Text Share</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Securely share passwords or secrets • Burn on
                        read</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <form id="shareTextForm" class="space-y-6">
                <div>
                    <label for="content" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-edit mr-2 text-purple-500"></i>Your Secret Content
                    </label>
                    <textarea id="content" name="content" rows="8" class="textarea-modern w-full"
                        placeholder="Paste your secret key, password, or confidential message here..."
                        required></textarea>
                    <div class="flex justify-between items-center mt-2 text-sm text-gray-500 dark:text-gray-400">
                        <span><i class="fas fa-info-circle mr-1"></i>Content is encrypted before sending</span>
                        <span id="charCount">0 characters</span>
                    </div>
                </div>

                <button type="submit" id="shareBtn"
                    class="btn-modern w-full sm:w-auto flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);">
                    <i class="fas fa-lock"></i>
                    <span>Encrypt & Share</span>
                </button>
            </form>

            <!-- Status Area -->
            <div id="statusArea" class="mt-6 hidden">
                <div class="glass-card rounded-xl p-5">
                    <div class="flex items-center gap-3">
                        <div class="spinner"></div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300" id="statusMessage"></p>
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
                            <h4 class="text-xl font-bold text-green-800 dark:text-green-300">Text Shared Successfully!
                            </h4>
                            <p class="text-sm text-green-600 dark:text-green-400">Share this PIN with the recipient</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-center my-6">
                        <span id="pinDisplay"
                            class="text-4xl font-mono font-bold text-gray-800 dark:text-white bg-white dark:bg-gray-800 px-8 py-4 rounded-2xl border-2 border-green-300 dark:border-green-700 shadow-lg select-all tracking-widest"></span>
                    </div>
                    <div
                        class="flex items-center justify-center gap-2 text-sm text-orange-600 dark:text-orange-400 mb-4">
                        <i class="fas fa-fire"></i>
                        <span>Text will be deleted after reading</span>
                    </div>
                    <button onclick="location.reload()" class="btn-modern w-full"
                        style="background: linear-gradient(135deg, #a855f7 0%, #ec4899 100%);">
                        <i class="fas fa-plus mr-2"></i>Share More Text
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips Section -->
    <div class="mt-6 grid sm:grid-cols-2 gap-4">
        <div class="glass-card rounded-xl p-5">
            <div class="flex items-start gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-lightbulb text-white"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Perfect for</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Passwords, API keys, private notes, and
                        confidential messages</p>
                </div>
            </div>
        </div>
        <div class="glass-card rounded-xl p-5">
            <div class="flex items-start gap-3">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-fire text-white"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Auto-Destruct</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Content is permanently deleted after it's viewed
                        once</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="./js/text.js"></script>

<script>
    // Character counter
    document.addEventListener('DOMContentLoaded', () => {
        const textarea = document.getElementById('content');
        const charCount = document.getElementById('charCount');

        if (textarea && charCount) {
            textarea.addEventListener('input', () => {
                charCount.textContent = `${textarea.value.length} characters`;
            });
        }
    });
</script>