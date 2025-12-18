<div
    class="bg-white dark:bg-black shadow sm:rounded-lg overflow-hidden max-w-xl mx-auto border border-gray-100 dark:border-gray-800 transition-colors duration-200">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
            Send a File
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
            Files are encrypted and stored securely.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6" id="uploadContainer">
        <form id="uploadForm" class="space-y-6">
            <div
                class="max-w-lg flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-md hover:border-primary-500 dark:hover:border-primary-500 transition-colors group">
                <div class="space-y-1 text-center">
                    <i
                        class="fas fa-cloud-upload-alt mx-auto text-5xl text-gray-400 dark:text-gray-500 group-hover:text-primary-500 transition-colors mb-4"></i>
                    <div class="flex text-sm text-gray-600 dark:text-gray-400 justify-center">
                        <label for="file"
                            class="relative cursor-pointer bg-white dark:bg-black rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500 dark:focus-within:ring-offset-gray-900">
                            <span id="filename-display">Upload a file</span>
                            <input id="file" name="file" type="file" class="sr-only" required>
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">
                        Max 10MB
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" id="uploadBtn"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Encrypt & Send
                </button>
            </div>
        </form>

        <!-- Progress / Status Area -->
        <div id="statusArea" class="mt-4 hidden">
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-md">
                <div class="flex flex-col">
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-blue-700" id="statusMessage">Preparing...</span>
                        <span class="text-sm font-medium text-blue-700" id="progressPercent">0%</span>
                    </div>
                    <div class="w-full bg-blue-200 rounded-full h-2.5 dark:bg-blue-900/50">
                        <div id="progressBar"
                            class="bg-blue-600 h-2.5 rounded-full transition-all duration-300 ease-out"
                            style="width: 0%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Result -->
        <div id="successResult"
            class="hidden mt-6 bg-green-50 dark:bg-green-900/20 border-1 border-green-200 dark:border-green-800 p-4 rounded-md">
            <h4 class="text-lg font-bold text-green-800 dark:text-green-400 mb-2">Upload Successful!</h4>
            <p class="text-sm text-green-700 dark:text-green-300">Your File PIN:</p>
            <div class="mt-2 flex items-center">
                <span id="pinDisplay"
                    class="text-2xl font-mono font-bold text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-800 px-4 py-2 rounded border border-gray-300 dark:border-gray-600 select-all"></span>
            </div>
            <p class="mt-4 text-xs text-green-600">Share this PIN with the recipient.</p>
            <button onclick="location.reload()"
                class="mt-4 text-sm text-primary-600 hover:text-primary-800 underline">Send another file</button>
        </div>

    </div>
</div>

<script src="./js/upload.js"></script>