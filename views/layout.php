<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure File Share</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="./js/theme-init.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        },
                        // Ensure pure black is available
                        black: '#000000',
                    }
                }
            }
        }
    </script>
</head>

<body
    class="h-full flex flex-col font-sans bg-gray-50 text-gray-900 dark:bg-black dark:text-gray-100 transition-colors duration-200">

    <!-- Header -->
    <nav
        class="bg-white dark:bg-black shadow-sm border-b border-gray-200 dark:border-gray-800 transition-colors duration-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="./" class="text-xl font-bold text-primary-600 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-2xl"></i>
                            SecureShare
                        </a>
                    </div>
                </div>
                <!-- Desktop Navigation -->
                <div class="hidden sm:flex items-center space-x-4">
                    <a href="./"
                        class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Send</a>
                    <a href="./receive"
                        class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Receive</a>
                    <a href="./share-text"
                        class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Share
                        Text</a>
                    <a href="./view-text"
                        class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">View
                        Text</a>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="./dashboard"
                            class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 px-3 py-2 rounded-md text-sm font-medium transition-colors">Dashboard</a>
                    <?php else: ?>
                        <a href="./login"
                            class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Login</a>
                    <?php endif; ?>

                    <!-- Theme Toggle Button -->
                    <button id="theme-toggle" type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon text-lg"></i>
                        <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-lg"></i>
                    </button>
                </div>
                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button type="button" id="mobile-menu-btn"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <!-- Icon when menu is closed. -->
                        <!-- Icon when menu is closed. -->
                        <i class="block fas fa-bars fa-lg" aria-hidden="true"></i>
                        <!-- Icon when menu is open. -->
                        <i class="hidden fas fa-times fa-lg" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div class="hidden sm:hidden" id="mobile-menu">
            <div class="pt-2 pb-3 space-y-1">
                <a href="./"
                    class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">Send</a>
                <a href="./receive"
                    class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">Receive</a>
                <a href="./share-text"
                    class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-800">Share
                    Text</a>
                <a href="./view-text"
                    class="block pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-800 dark:hover:text-white">View
                    Text</a>
                <!-- Mobile Theme Toggle -->
                <button id="mobile-theme-toggle"
                    class="w-full text-left pl-3 pr-4 py-2 border-l-4 border-transparent text-base font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-900 hover:border-gray-300 dark:hover:border-gray-600 hover:text-gray-800 dark:hover:text-white">
                    Toggle Theme
                </button>
            </div>
        </div>
    </nav>

    <script src="./js/app.js"></script>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8 max-w-3xl">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700"><?= htmlspecialchars($_GET['message'] ?? 'An error occurred') ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-md shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">Success! Your PIN is: <strong
                                class="text-lg ml-1 font-mono bg-white px-2 py-0.5 rounded border border-green-200"><?= htmlspecialchars($_GET['pin'] ?? '') ?></strong>
                        </p>
                        <?php if (isset($_GET['filename'])): ?>
                            <p class="text-sm text-green-600 mt-1">File:
                                <strong><?= htmlspecialchars($_GET['filename']) ?></strong>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- View Content -->
        <?php require __DIR__ . '/' . $view . '.php'; ?>
    </main>

    <!-- Footer -->
    <footer
        class="bg-white dark:bg-black border-t border-gray-200 dark:border-gray-800 mt-auto transition-colors duration-200">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">&copy; <?= date('Y') ?> Secure Share. Enterprise Grade
                Security.</p>
        </div>
    </footer>

</body>

</html>