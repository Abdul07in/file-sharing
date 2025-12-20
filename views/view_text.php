<div class="w-full max-w-3xl mx-auto animate-slide-up">
    <div class="glass-card rounded-2xl overflow-hidden">
        <!-- Header -->
        <div
            class="px-6 py-5 bg-gradient-to-r from-cyan-500/10 to-blue-500/10 dark:from-cyan-900/30 dark:to-blue-900/30 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div
                    class="w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-eye text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Secure Text View</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400" id="headerText">Enter the 4-digit PIN to view
                        shared text</p>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-6">
            <!-- Error Area -->
            <div id="errorArea" class="hidden mb-6 animate-scale-in">
                <div class="glass-card bg-red-50/80 dark:bg-red-900/30 border-l-4 border-red-500 px-4 py-3 rounded-xl">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                        <p class="text-sm text-red-700 dark:text-red-300" id="errorMessage"></p>
                    </div>
                </div>
            </div>

            <!-- Input Form -->
            <form id="viewTextForm" class="space-y-6">
                <div>
                    <label for="pin" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        <i class="fas fa-key mr-2 text-cyan-500"></i>Content PIN
                    </label>
                    <input type="text" name="pin" id="pin" maxlength="4" pattern="\d{4}" class="pin-input w-full"
                        placeholder="0000" required autocomplete="off">
                </div>

                <button type="submit" id="viewBtn" class="btn-modern w-full flex items-center justify-center gap-2"
                    style="background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);">
                    <i class="fas fa-unlock"></i>
                    <span>Decrypt & View</span>
                </button>
            </form>

            <!-- Result Content (Hidden by default) -->
            <div id="resultContent" class="hidden space-y-4 success-animate">
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" onclick="copyText()"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all font-medium">
                        <i class="fas fa-copy"></i>Copy Text
                    </button>
                    <button type="button" onclick="toggleMarkdown()"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 transition-all font-medium">
                        <i class="fas fa-eye"></i>Preview Markdown
                    </button>
                </div>

                <!-- Raw Text View -->
                <div id="rawText"
                    class="glass-card rounded-xl p-5 font-mono text-sm overflow-auto bg-gray-50/80 dark:bg-gray-900/50"
                    style="max-height: 400px; white-space: pre-wrap;"></div>

                <!-- Markdown Preview (Hidden) -->
                <div id="markdownPreview"
                    class="hidden glass-card rounded-xl p-6 prose prose-indigo dark:prose-invert max-w-none bg-white dark:bg-gray-800">
                </div>

                <!-- Burn Warning -->
                <div
                    class="glass-card rounded-xl p-5 bg-orange-50/80 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-fire text-white"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-orange-800 dark:text-orange-300">Burn on Read</h4>
                            <p class="text-sm text-orange-700 dark:text-orange-400 mt-1">This content has been
                                permanently deleted from the server. Do not refresh the page.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <a href="./view-text"
                        class="inline-flex items-center gap-2 text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-semibold transition-all">
                        <i class="fas fa-arrow-left"></i>View another
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Marked.js -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="./js/text.js"></script>