(() => {
    'use strict';

    const storageKey = 'swiftorder-theme';
    const root = document.documentElement;
    const toggle = document.querySelector('[data-theme-toggle]');

    const getStoredTheme = () => {
        try {
            const value =
                localStorage.getItem(storageKey);
            return value === 'dark' || value === 'light' ? value : null;
        } catch (_error) {
            return null;
        }
    };

    const getPreferredTheme = () => {
        const stored = getStoredTheme();
        if (stored !== null) {
            return stored;
        }
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    };

    const applyTheme = (theme) => {
        root.dataset.theme = theme;
        root.style.colorScheme = theme;

        if (toggle instanceof HTMLButtonElement) {
            const dark = theme === 'dark';
            const nextTheme = dark ? 'light' : 'dark';
            const icon = toggle.querySelector('.theme-toggle-icon');

            toggle.setAttribute('aria-pressed', String(dark));
            toggle.setAttribute('aria-label', `Switch to ${nextTheme} mode`);
            toggle.setAttribute('title', `Switch to ${nextTheme} mode`);

            if (icon instanceof HTMLElement) {
                icon.textContent = dark ? '☀' : '☾';
            }
        }
    };

    applyTheme(getPreferredTheme());

    if (toggle instanceof HTMLButtonElement) {
        toggle.addEventListener('click', () => {
            const nextTheme = root.dataset.theme === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme);

            try {
                localStorage.setItem(storageKey, nextTheme);
            } catch (_error) {
                // Current-page theme remains active when storage is unavailable.
            }
        });
    }
})();
