<<<<<<< HEAD
<div class="w-full max-w-md mx-auto animate-slide-up">
    <div class="glass-card rounded-2xl overflow-hidden">
        <!-- Header -->
        <div
            class="px-6 py-8 text-center bg-gradient-to-br from-primary-500/5 to-purple-500/5 dark:from-primary-900/20 dark:to-purple-900/20">
            <div
                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center shadow-lg mx-auto mb-4">
                <i class="fas fa-user text-white text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome Back</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Sign in to your SecureShare account</p>
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

            <?php if (isset($_GET['success'])): ?>
                <div class="glass-card bg-green-50/80 dark:bg-green-900/30 border-l-4 border-green-500 px-4 py-3 rounded-xl mb-6 animate-scale-in success-animate"
                    role="alert">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        <span
                            class="text-sm text-green-700 dark:text-green-300"><?= htmlspecialchars($_GET['success']) ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <form action="./login" method="POST" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" for="username">
                        <i class="fas fa-user mr-2 text-primary-500"></i>Username
                    </label>
                    <input class="modern-input w-full" id="username" name="username" type="text"
                        placeholder="Enter your username" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2" for="password">
                        <i class="fas fa-lock mr-2 text-primary-500"></i>Password
                    </label>
                    <input class="modern-input w-full" id="password" name="password" type="password"
                        placeholder="Enter your password" required>
                </div>

                <button type="submit" class="btn-modern w-full flex items-center justify-center gap-2 mt-6">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Sign In</span>
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center">
                <p class="text-gray-500 dark:text-gray-400 text-sm">
                    Don't have an account?
                    <a href="./register"
                        class="font-semibold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 ml-1 transition-colors">
                        Create one now <i class="fas fa-arrow-right text-xs ml-1"></i>
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Features List -->
    <div class="mt-6 grid grid-cols-3 gap-4 text-center">
        <div class="glass-card rounded-xl p-4">
            <i class="fas fa-lock text-primary-500 text-xl mb-2"></i>
            <p class="text-xs text-gray-600 dark:text-gray-400">End-to-End Encrypted</p>
        </div>
        <div class="glass-card rounded-xl p-4">
            <i class="fas fa-bolt text-yellow-500 text-xl mb-2"></i>
            <p class="text-xs text-gray-600 dark:text-gray-400">Instant Transfer</p>
        </div>
        <div class="glass-card rounded-xl p-4">
            <i class="fas fa-fire text-orange-500 text-xl mb-2"></i>
            <p class="text-xs text-gray-600 dark:text-gray-400">Burn After Read</p>
        </div>
    </div>
=======
<div class="max-w-md mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-md p-8">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-900 dark:text-white">Login to SecureShare</h2>

    <?php if (isset($_GET['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($_GET['error']) ?></span>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline"><?= htmlspecialchars($_GET['success']) ?></span>
        </div>
    <?php endif; ?>

    <form action="./login" method="POST">
        <div class="mb-4">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="username">
                Username
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary-500"
                id="username" name="username" type="text" placeholder="Username" required>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2" for="password">
                Password
            </label>
            <input
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-200 dark:bg-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-primary-500"
                id="password" name="password" type="password" placeholder="******************" required>
        </div>
        <div class="flex items-center justify-between">
            <button
                class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150 ease-in-out w-full"
                type="submit">
                Sign In
            </button>
        </div>
        <div class="mt-4 text-center">
            <a href="./register"
                class="inline-block align-baseline font-bold text-sm text-primary-600 hover:text-primary-800">
                Don't have an account? Register
            </a>
        </div>
    </form>
>>>>>>> origin/main
</div>