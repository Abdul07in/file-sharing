<div class="bg-white shadow sm:rounded-lg overflow-hidden max-w-md mx-auto">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-100">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Receive File
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Enter the 4-digit PIN to download.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6">
        <form action="./receive" method="POST">
            <div>
                <label for="pin" class="block text-sm font-medium text-gray-700">File PIN</label>
                <div class="mt-1">
                    <input type="text" name="pin" id="pin" maxlength="4" pattern="\d{4}"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-lg border-gray-300 rounded-md text-center tracking-widest py-3"
                        placeholder="0000" required>
                </div>
            </div>
            <div class="mt-6">
                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Download File
                </button>
            </div>
        </form>
    </div>
</div>