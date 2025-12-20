<<<<<<< HEAD
<div class="w-full max-w-md mx-auto animate-slide-up">
    <div class="glass-card rounded-2xl overflow-hidden">
        <!-- Header -->
        <div
            class="px-6 py-8 text-center bg-gradient-to-br from-green-500/5 to-emerald-500/5 dark:from-green-900/20 dark:to-emerald-900/20">
            <div
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shadow-lg mx-auto mb-4">
                <i class="fas fa-user-plus text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create Account</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Join SecureShare for secure file sharing</p>
        </div>

        <!-- Content -->
        <div class="p-6">
            <?php if (isset($_GET['error'])): ?>
                <div class="glass-card bg-red-50/80 dark:bg-red-900/30 border-l-4 border-red-500 px-4 py-3 rounded-xl mb-6 animate-scale-in"
                    role="alert">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                        <span class="text-sm text-red-700 dark:text-red-300"><?= htmlspecialchars($_GET['error']) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form action="./register" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" for="username">
                        <i class="fas fa-user mr-2 text-green-500"></i>Username
                    </label>
                    <input class="modern-input w-full" id="username" name="username" type="text"
                        placeholder="Choose a username" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" for="password">
                        <i class="fas fa-lock mr-2 text-green-500"></i>Password
                    </label>
                    <input class="modern-input w-full" id="password" name="password" type="password"
                        placeholder="Create a strong password" required minlength="6">
                    <p class="text-xs text-gray-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Minimum 6 characters
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2"
                        for="confirm_password">
                        <i class="fas fa-lock mr-2 text-green-500"></i>Confirm Password
                    </label>
                    <input class="modern-input w-full" id="confirm_password" name="confirm_password" type="password"
                        placeholder="Confirm your password" required>
                </div>

                <button type="submit" class="btn-modern w-full flex items-center justify-center gap-2 mt-6"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-rocket"></i>
                    <span>Create Account</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    Already have an account?
                    <a href="./login"
                        class="font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 ml-1 transition-colors">
                        Sign in <i class="fas fa-arrow-right text-xs ml-1"></i>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Security Info -->
    <div class="mt-6 glass-card rounded-2xl p-5">
        <div class="flex items-start gap-4">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shield-alt text-white"></i>
            </div>
            <div>
                <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Your Security Matters</h4>
                <p class="text-sm text-gray-500 dark:text-gray-400">Your password is securely hashed. We never store
                    plain text passwords.</p>
            </div>
        </div>
    </div>
=======
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
>>>>>>> origin/main
</div>