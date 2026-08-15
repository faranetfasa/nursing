import './bootstrap';

/**
 * Light/dark theme. The initial value comes from the settings table (rendered
 * into the layout) and is then remembered per browser.
 */
const storageKey = 'nursing.theme';

window.nursingTheme = {
    current() {
        return localStorage.getItem(storageKey) ?? document.documentElement.dataset.defaultTheme ?? 'light';
    },
    apply(theme) {
        document.documentElement.classList.toggle('dark', theme === 'dark');
        localStorage.setItem(storageKey, theme);
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme } }));
    },
    toggle() {
        this.apply(this.current() === 'dark' ? 'light' : 'dark');
    },
};

window.nursingTheme.apply(window.nursingTheme.current());
