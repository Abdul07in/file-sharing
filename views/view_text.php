<div
    class="bg-white dark:bg-black shadow sm:rounded-lg overflow-hidden max-w-2xl mx-auto border border-gray-100 dark:border-gray-800 transition-colors duration-200">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
            Secure Text View
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400" id="headerText">
            Enter the 4-digit PIN to view shared text.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6">

        <!-- Error Area -->
        <div id="errorArea" class="hidden rounded-md bg-red-50 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700" id="errorMessage"></p>
                </div>
            </div>
        </div>

        <!-- Input Form -->
        <form id="viewTextForm">
            <div>
                <label for="pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content PIN</label>
                <div class="mt-1">
                    <input type="text" name="pin" id="pin" maxlength="4" pattern="\d{4}"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md text-center tracking-widest py-3"
                        placeholder="0000" required autocomplete="off">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" id="viewBtn"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Decrypt & View
                </button>
            </div>
        </form>

        <!-- Result Content (Hidden by default) -->
        <div id="resultContent" class="hidden space-y-4">
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
                <button type="button" onclick="copyText()"
                    class="inline-flex items-center justify-center w-full sm:w-auto px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Copy Text
                </button>
                <button type="button" onclick="toggleMarkdown()"
                    class="inline-flex items-center justify-center w-full sm:w-auto px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Preview Markdown
                </button>
            </div>

            <!-- Raw Text View -->
            <div id="rawText"
                class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm bg-gray-50 dark:bg-gray-900 dark:text-gray-100 p-4 font-mono text-sm overflow-auto"
                style="max-height: 500px; white-space: pre-wrap;"></div>

            <!-- Markdown Preview (Hidden) -->
            <div id="markdownPreview"
                class="hidden mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm bg-white dark:bg-black p-6 prose prose-indigo dark:prose-invert max-w-none">
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
                            <p>This content has been permanently deleted from the server. Do not refresh.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <a href="./view-text" class="text-primary-600 hover:text-primary-500 font-medium">&larr; View
                    another</a>
            </div>
        </div>

    </div>
</div>

<!-- Marked.js -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="./js/text.js"></script>