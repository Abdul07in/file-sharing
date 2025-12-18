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
                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
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
                        <i class="fas fa-fire text-yellow-400 text-xl"></i>
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