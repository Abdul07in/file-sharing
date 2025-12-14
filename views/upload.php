<div class="bg-white shadow sm:rounded-lg overflow-hidden max-w-xl mx-auto">
    <div class="px-4 py-5 sm:px-6 bg-gray-50 border-b border-gray-100">
        <h3 class="text-lg leading-6 font-medium text-gray-900">
            Send a File
        </h3>
        <p class="mt-1 max-w-2xl text-sm text-gray-500">
            Files are encrypted and stored securely.
        </p>
    </div>
    <div class="px-4 py-5 sm:p-6">
        <form action="./upload" method="POST" enctype="multipart/form-data"
            onsubmit="const btn = document.getElementById('uploadBtn'); btn.innerHTML = 'Encrypting & Sending...'; btn.classList.add('opacity-75', 'cursor-not-allowed');">
            <div
                class="max-w-lg flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-primary-500 transition-colors group">
                <div class="space-y-1 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-primary-500 transition-colors"
                        stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                        <path
                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <div class="flex text-sm text-gray-600 justify-center">
                        <label for="file"
                            class="relative cursor-pointer bg-white rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                            <span id="filename-display">Upload a file</span>
                            <input id="file" name="file" type="file" class="sr-only" required>
                        </label>
                        <p class="pl-1">or drag and drop</p>
                    </div>
                    <p class="text-xs text-gray-500">
                        Max 64MB
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
    </div>
</div>

<script>
    document.getElementById('file').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const fileName = e.target.files[0].name;
            const labelSpan = document.getElementById('filename-display');
            if (labelSpan) {
                labelSpan.textContent = fileName;
                labelSpan.classList.add('text-gray-900');
            }
        }
    });
</script>