<div
    class="bg-white dark:bg-black shadow sm:rounded-lg overflow-hidden max-w-xl mx-auto border border-gray-100 dark:border-gray-800 transition-colors duration-200">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
            Receive a File
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
            Enter your PIN to decrypt and download.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6">
        <form id="downloadForm" class="space-y-6">
            <div>
                <label for="pin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">File PIN</label>
                <div class="mt-1 relative rounded-md shadow-sm">
                    <input type="text" name="pin" id="pin"
                        class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-4 pr-12 sm:text-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-md py-3 font-mono tracking-widest text-center uppercase"
                        placeholder="ENTER PIN" required autocomplete="off">
                </div>
                <p class="mt-2 text-xs text-gray-500 text-center">
                    Files are deleted immediately after download.
                </p>
            </div>

            <div class="mt-6">
                <button type="submit" id="downloadBtn"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Decrypt & Download
                </button>
            </div>
        </form>

        <!-- Progress / Status Area -->
        <div id="statusArea" class="mt-4 hidden">
            <div class="p-4 rounded-md">
                <div class="flex">
                    <div class="ml-3">
                        <p class="text-sm font-medium" id="statusMessage"></p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="./js/download.js"></script>