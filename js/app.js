document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Logic
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');
    const iconOpen = document.getElementById('menu-icon-open');
    const iconClose = document.getElementById('menu-icon-close');

    if (btn && menu) {
        btn.addEventListener('click', () => {
            const isOpen = menu.classList.contains('open');

            if (isOpen) {
                menu.classList.remove('open');
                menu.classList.add('hidden');
                iconOpen?.classList.remove('hidden');
                iconClose?.classList.add('hidden');
            } else {
                menu.classList.remove('hidden');
                // Trigger reflow for animation
                menu.offsetHeight;
                menu.classList.add('open');
                iconOpen?.classList.add('hidden');
                iconClose?.classList.remove('hidden');
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!menu.contains(e.target) && !btn.contains(e.target) && menu.classList.contains('open')) {
                menu.classList.remove('open');
                menu.classList.add('hidden');
                iconOpen?.classList.remove('hidden');
                iconClose?.classList.add('hidden');
            }
        });
    }

    // Theme Toggle Logic
    var themeToggleBtn = document.getElementById('theme-toggle');
    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
    var mobileThemeToggle = document.getElementById('mobile-theme-toggle');
    var mobileThemeToggleBtn = document.getElementById('mobile-theme-toggle-btn');

    // Change the icons inside the button based on previous settings
    if (localStorage.theme === 'dark' || (!('theme' in localStorage))) {
        if (themeToggleLightIcon) themeToggleLightIcon.classList.remove('hidden');
    } else {
        if (themeToggleDarkIcon) themeToggleDarkIcon.classList.remove('hidden');
    }

    function toggleTheme() {
        // toggle icons inside button
        if (themeToggleDarkIcon) themeToggleDarkIcon.classList.toggle('hidden');
        if (themeToggleLightIcon) themeToggleLightIcon.classList.toggle('hidden');

        // if set via local storage previously
        if (localStorage.theme === 'dark' || (!('theme' in localStorage))) {
            document.documentElement.classList.remove('dark');
            localStorage.theme = 'light';
        } else {
            document.documentElement.classList.add('dark');
            localStorage.theme = 'dark';
        }
    }

    if (themeToggleBtn) themeToggleBtn.addEventListener('click', toggleTheme);
    if (mobileThemeToggle) mobileThemeToggle.addEventListener('click', toggleTheme);
    if (mobileThemeToggleBtn) mobileThemeToggleBtn.addEventListener('click', toggleTheme);

    // Add smooth entrance animation to main content cards
    const cards = document.querySelectorAll('.glass-card, .hover-lift');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
