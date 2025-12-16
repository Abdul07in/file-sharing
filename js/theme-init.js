// Check for saved theme preference, otherwise default to dark
if (localStorage.theme === 'dark' || (!('theme' in localStorage))) {
    document.documentElement.classList.add('dark')
} else {
    document.documentElement.classList.remove('dark')
}

// Config for Tailwind (if needed, though this is usually inline, but we can move it if it works)
// Actually tailwind.config in browser script needs to be inline or before the script tag.
// We will keep the tailwind config inline as it configures the library before it runs,
// but the theme check can be external if loaded early.
// However, to avoid FOUC, this script must be blocking.
