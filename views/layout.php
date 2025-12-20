<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure File Share</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.10/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.10/dist/sweetalert2.all.min.js"></script>
    <!-- Custom Modern CSS -->
    <link rel="stylesheet" href="./css/modern.css">
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
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        black: '#000000',
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-out',
                        'slide-up': 'slideUp 0.4s ease-out',
                        'scale-in': 'scaleIn 0.3s ease-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideUp: {
                            '0%': { transform: 'translateY(20px)', opacity: '0' },
                            '100%': { transform: 'translateY(0)', opacity: '1' },
                        },
                        scaleIn: {
                            '0%': { transform: 'scale(0.95)', opacity: '0' },
                            '100%': { transform: 'scale(1)', opacity: '1' },
                        },
                    },
                }
            }
        }
    </script>
</head>

<body class="bg-animated min-h-screen text-gray-900 dark:text-gray-100 flex flex-col">

    <!-- Header with Gradient Bar -->
    <nav class="nav-glass shadow-lg sticky top-0 z-50">
        <div class="header-gradient-bar"></div>
        <div class="w-full px-4 sm:px-6 lg:px-12">
            <div class="flex justify-between h-16">
                <div class="flex">
                    <div class="flex-shrink-0 flex items-center">
                        <a href="./" class="text-xl font-bold flex items-center gap-3 group">
                            <div
                                class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center shadow-lg group-hover:shadow-primary-500/50 transition-all duration-300 group-hover:scale-110">
                                <i class="fas fa-shield-alt text-white text-lg"></i>
                            </div>
                            <span class="gradient-text font-extrabold tracking-tight">SecureShare</span>
                        </a>
                    </div>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="./"
                        class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                        <i class="fas fa-paper-plane mr-2"></i>Send
                    </a>
                    <a href="./receive"
                        class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                        <i class="fas fa-download mr-2"></i>Receive
                    </a>
                    <a href="./share-text"
                        class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                        <i class="fas fa-comment-dots mr-2"></i>Share Text
                    </a>
                    <a href="./view-text"
                        class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                        <i class="fas fa-eye mr-2"></i>View Text
                    </a>

                    <div class="w-px h-6 bg-gray-300 dark:bg-gray-600 mx-2"></div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="./dashboard"
                            class="text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 px-4 py-2 rounded-xl text-sm font-semibold transition-all duration-200">
                            <i class="fas fa-th-large mr-2"></i>Dashboard
                        </a>
                        <div class="flex items-center gap-3 ml-2 pl-3 border-l border-gray-200 dark:border-gray-700">
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center text-white text-sm font-bold">
                                <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                            </div>
                            <span
                                class="text-gray-700 dark:text-gray-300 text-sm font-medium"><?= htmlspecialchars($_SESSION['username']) ?></span>
                            <a href="./logout"
                                class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 p-2 rounded-lg text-sm font-medium transition-all duration-200"
                                title="Logout">
                                <i class="fas fa-sign-out-alt"></i>
                            </a>
                        </div>
                    <?php else: ?>
                        <a href="./login"
                            class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 px-4 py-2 rounded-xl text-sm font-medium transition-all duration-200">
                            Login
                        </a>
                        <a href="./register" class="btn-modern text-sm py-2 px-5 rounded-xl">
                            Get Started
                        </a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="./dashboard"
                            class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 px-3 py-2 rounded-md text-sm font-medium transition-colors">Dashboard</a>
                    <?php else: ?>
                        <a href="./login"
                            class="text-gray-600 dark:text-gray-300 hover:text-primary-600 dark:hover:text-primary-400 px-3 py-2 rounded-md text-sm font-medium transition-colors">Login</a>
                    <?php endif; ?>

                    <!-- Theme Toggle Button -->
                    <button id="theme-toggle" type="button"
                        class="ml-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 p-2.5 rounded-xl transition-all duration-200 hover:scale-110">
                        <i id="theme-toggle-dark-icon" class="hidden fas fa-moon text-lg"></i>
                        <i id="theme-toggle-light-icon" class="hidden fas fa-sun text-lg"></i>
                    </button>
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center md:hidden gap-2">
                    <!-- Mobile Theme Toggle -->
                    <button id="mobile-theme-toggle-btn" type="button"
                        class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 p-2.5 rounded-xl transition-all duration-200">
                        <i class="fas fa-moon dark:hidden text-lg"></i>
                        <i class="fas fa-sun hidden dark:block text-lg"></i>
                    </button>
                    <button type="button" id="mobile-menu-btn"
                        class="inline-flex items-center justify-center p-2.5 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all duration-200"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars fa-lg" id="menu-icon-open"></i>
                        <i class="fas fa-times fa-lg hidden" id="menu-icon-close"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div class="hidden md:hidden mobile-menu-slide" id="mobile-menu">
            <div
                class="pt-2 pb-4 space-y-1 px-4 bg-white/95 dark:bg-gray-900/95 backdrop-blur-xl border-t border-gray-200 dark:border-gray-700">
                <a href="./"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200">
                    <i class="fas fa-paper-plane w-5"></i>Send
                </a>
                <a href="./receive"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200">
                    <i class="fas fa-download w-5"></i>Receive
                </a>
                <a href="./share-text"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200">
                    <i class="fas fa-comment-dots w-5"></i>Share Text
                </a>
                <a href="./view-text"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-200">
                    <i class="fas fa-eye w-5"></i>View Text
                </a>

                <div class="border-t border-gray-200 dark:border-gray-700 my-3"></div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="flex items-center gap-3 px-4 py-3">
                        <div
                            class="w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center text-white text-lg font-bold">
                            <?= strtoupper(substr($_SESSION['username'], 0, 1)) ?>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                <?= htmlspecialchars($_SESSION['username']) ?></p>
                            <p class="text-xs text-gray-500">Logged in</p>
                        </div>
                    </div>
                    <a href="./dashboard"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-semibold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-all duration-200">
                        <i class="fas fa-th-large w-5"></i>Dashboard
                    </a>
                    <a href="./logout"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200">
                        <i class="fas fa-sign-out-alt w-5"></i>Logout
                    </a>
                <?php else: ?>
                    <a href="./login"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition-all duration-200">
                        <i class="fas fa-sign-in-alt w-5"></i>Login
                    </a>
                    <a href="./register"
                        class="flex items-center justify-center gap-2 mx-4 py-3 rounded-xl text-base font-semibold text-white bg-gradient-to-r from-primary-500 to-purple-600 hover:from-primary-600 hover:to-purple-700 transition-all duration-200 shadow-lg">
                        <i class="fas fa-rocket"></i>Get Started
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script src="./js/app.js"></script>

    <!-- Main Content -->
    <main class="flex-grow w-full px-4 sm:px-6 lg:px-12 py-8 animate-fade-in">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'error'): ?>
            <div
                class="glass-card bg-red-50/80 dark:bg-red-900/30 border-l-4 border-red-500 p-4 mb-6 rounded-xl max-w-4xl mx-auto animate-slide-up">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/50 flex items-center justify-center">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    </div>
                    <p class="text-sm text-red-700 dark:text-red-300 font-medium">
                        <?= htmlspecialchars($_GET['message'] ?? 'An error occurred') ?></p>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div
                class="glass-card bg-green-50/80 dark:bg-green-900/30 border-l-4 border-green-500 p-4 mb-6 rounded-xl max-w-4xl mx-auto animate-slide-up success-animate">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-green-700 dark:text-green-300">Success! Your PIN is:
                            <strong
                                class="text-lg ml-2 font-mono bg-white dark:bg-gray-800 px-3 py-1 rounded-lg border border-green-200 dark:border-green-700">
                                <?= htmlspecialchars($_GET['pin'] ?? '') ?>
                            </strong>
                        </p>
                        <?php if (isset($_GET['filename'])): ?>
                            <p class="text-sm text-green-600 dark:text-green-400 mt-1">File:
                                <strong><?= htmlspecialchars($_GET['filename']) ?></strong></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['msg'])): ?>
            <div
                class="glass-card bg-blue-50/80 dark:bg-blue-900/30 border-l-4 border-blue-500 p-4 mb-6 rounded-xl max-w-4xl mx-auto animate-slide-up">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                        <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                    </div>
                    <p class="text-sm text-blue-700 dark:text-blue-300 font-medium"><?= htmlspecialchars($_GET['msg']) ?>
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <!-- View Content -->
        <?php require __DIR__ . '/' . $view . '.php'; ?>
    </main>

    <!-- Footer -->
    <footer class="nav-glass border-t border-gray-200 dark:border-gray-800 mt-auto">
        <div class="w-full px-4 sm:px-6 lg:px-12 py-6">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-purple-600 flex items-center justify-center">
                        <i class="fas fa-shield-alt text-white text-sm"></i>
                    </div>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">SecureShare</span>
                </div>
                <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                    &copy; <?= date('Y') ?> SecureShare. Enterprise Grade Security.
                </p>
                <div class="flex items-center gap-4">
                    <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors duration-200">
                        <i class="fab fa-github text-xl"></i>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-primary-500 transition-colors duration-200">
                        <i class="fab fa-twitter text-xl"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>

</html>