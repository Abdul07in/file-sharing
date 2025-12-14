<div class="bg-white shadow sm:rounded-lg overflow-hidden max-w-2xl mx-auto">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-100">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Safe Text Share
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Securely share passwords or secrets. Burn on read.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6">
        <form action="./share-text" method="POST">
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700">Content</label>
                <div class="mt-1">
                    <textarea id="content" name="content" rows="8"
                        class="shadow-sm focus:ring-primary-500 focus:border-primary-500 block w-full sm:text-sm border-gray-300 rounded-md p-3"
                        placeholder="Paste your secret key, password, or message here..." required></textarea>
                </div>
            </div>
            <div class="mt-6 flex flex-col sm:flex-row sm:justify-end">
                <button type="submit"
                    class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    Encrypt & Share
                </button>
            </div>
        </form>
    </div>
</div>