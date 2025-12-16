<div
    class="bg-white dark:bg-black shadow sm:rounded-lg overflow-hidden max-w-2xl mx-auto border border-gray-100 dark:border-gray-800 transition-colors duration-200">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
            Safe Text Share
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
            Securely share passwords or secrets. Burn on read.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6">
        <form id="shareTextForm" class="space-y-6">
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                <div class="mt-1">
                    <textarea id="content" name="content" rows="8"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md p-3"
                        placeholder="Paste your secret key, password, or message here..." required></textarea>
                </div>
            </div>
            <div class="mt-6 flex flex-col sm:flex-row sm:justify-end">
                <button type="submit" id="shareBtn"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Encrypt & Share
                </button>
            </div>
        </form>

        <!-- Status Area -->
        <div id="statusArea" class="mt-4 hidden p-4 rounded-md">
            <p id="statusMessage" class="text-sm font-medium"></p>
        </div>

        <!-- Success Result -->
        <div id="successResult"
            class="hidden mt-6 bg-green-50 dark:bg-green-900/20 border-1 border-green-200 dark:border-green-800 p-4 rounded-md">
            <h4 class="text-lg font-bold text-green-800 dark:text-green-400 mb-2">Text Shared Successfully!</h4>
            <div class="mt-2 flex items-center gap-4">
                <span class="text-sm text-green-700 dark:text-green-300">PIN:</span>
                <span id="pinDisplay"
                    class="text-2xl font-mono font-bold text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 px-4 py-2 rounded border border-gray-300 dark:border-gray-600 select-all"></span>
            </div>
            <p class="mt-4 text-xs text-green-600">Share this PIN. Text will be deleted after reading.</p>
            <button onclick="location.reload()"
                class="mt-4 text-sm text-primary-600 hover:text-primary-800 underline">Share more text</button>
        </div>
    </div>
</div>

<script src="./js/text.js"></script>