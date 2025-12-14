<div class="bg-white shadow sm:rounded-lg overflow-hidden max-w-2xl mx-auto">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-100">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Secure Text View
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            <?php if (isset($textPromise)): ?>
                Decrypted content below.
            <?php else: ?>
                Enter the 4-digit PIN to view shared text.
            <?php endif; ?>
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6">
        <?php if (isset($error)): ?>
            <div class="rounded-md bg-red-50 p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Error</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <p><?= htmlspecialchars($error) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($textPromise)): ?>
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="copyText()"
                        class="inline-flex items-center justify-center w-full sm:w-auto px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                        </svg>
                        Copy Text
                    </button>
                    <button type="button" onclick="toggleMarkdown()"
                        class="inline-flex items-center justify-center w-full sm:w-auto px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <svg class="-ml-0.5 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview Markdown
                    </button>
                </div>

                <!-- Raw Text View -->
                <div id="rawText"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm bg-gray-50 p-4 font-mono text-sm overflow-auto"
                    style="max-height: 500px; white-space: pre-wrap;"><?= htmlspecialchars($textPromise) ?></div>

                <!-- Markdown Preview (Hidden) -->
                <div id="markdownPreview"
                    class="hidden mt-1 w-full rounded-md border-gray-300 shadow-sm bg-white p-6 prose prose-indigo max-w-none">
                </div>

                <div class="rounded-md bg-yellow-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Burn on Read</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This content has been permanently deleted from the server. Do not refresh the page.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="./share-text" class="text-primary-600 hover:text-primary-500 font-medium">Share another secret
                    &rarr;</a>
            </div>

            <!-- Marked.js -->
            <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
            <script>
                function copyText() {
                    const text = document.getElementById('rawText').innerText;
                    navigator.clipboard.writeText(text).then(() => {
                        // Could change button text briefly
                        alert('Copied!');
                    });
                }

                function toggleMarkdown() {
                    const rawView = document.getElementById('rawText');
                    const mdView = document.getElementById('markdownPreview');
                    const btn = event.currentTarget; // Get button

                    if (mdView.classList.contains('hidden')) {
                        // Show Markdown
                        const text = rawView.innerText;
                        mdView.innerHTML = marked.parse(text);

                        rawView.classList.add('hidden');
                        mdView.classList.remove('hidden');
                        // Update button text logic if needed
                    } else {
                        // Show Raw
                        mdView.classList.add('hidden');
                        rawView.classList.remove('hidden');
                    }
                }
            </script>
        <?php else: ?>
            <form action="./view-text" method="POST">
                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700">Content PIN</label>
                    <div class="mt-1">
                        <input type="text" name="pin" id="pin" maxlength="4" pattern="\d{4}"
                            class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-lg border-gray-300 rounded-md text-center tracking-widest py-3"
                            placeholder="0000" required>
                    </div>
                </div>
                <div class="mt-6">
                    <button type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                        Decrypt & View
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>