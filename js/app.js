document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Logic
    const btn = document.getElementById('mobile-menu-btn');
    const menu = document.getElementById('mobile-menu');

    if (btn && menu) {
        const svgs = btn.querySelectorAll('svg');
        btn.addEventListener('click', () => {
            menu.classList.toggle('hidden');
            svgs.forEach(svg => svg.classList.toggle('hidden'));
            svgs.forEach(svg => svg.classList.toggle('block'));
        });
    }

    // Theme Toggle Logic
    var themeToggleBtn = document.getElementById('theme-toggle');
    var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
    var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
    var mobileThemeToggle = document.getElementById('mobile-theme-toggle');

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
});
