<div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-900 dark:text-white">Create an Account</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <form action="./register" method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="username">
                Username
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary-500"
                id="username" name="username" type="text" placeholder="Username" required>
        </div>
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="password">
                Password
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary-500"
                id="password" name="password" type="password" placeholder="******************" required minlength="6">
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="confirm_password">
                Confirm Password
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary-500"
                id="confirm_password" name="confirm_password" type="password" placeholder="******************" required>
        </div>
        <div class="flex items-center justify-between">
            <button
                class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out w-full"
                type="submit">
                Register
            </button>
        </div>
        <div class="mt-4 text-center">
            <a href="./login"
                class="inline-block align-baseline font-bold text-sm text-primary-600 hover:text-primary-800">
                Already have an account? Login
            </a>
        </div>
    </form>
</div>