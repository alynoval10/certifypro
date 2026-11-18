const sidebar = document.querySelector('[data-sidebar]');
const sidebarOverlay = document.querySelector('[data-sidebar-overlay]');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');

const closeSidebar = () => {
    sidebar?.classList.add('-translate-x-full');
    sidebarOverlay?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
};

sidebarToggle?.addEventListener('click', () => {
    sidebar?.classList.remove('-translate-x-full');
    sidebarOverlay?.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
});
sidebarOverlay?.addEventListener('click', closeSidebar);

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        closeSidebar();
    }
});

const passwordToggle = document.querySelector('[data-password-toggle]');

passwordToggle?.addEventListener('click', () => {
    const passwordInput = passwordToggle.parentElement?.querySelector('input');

    if (!passwordInput) {
        return;
    }

    const shouldShowPassword = passwordInput.type === 'password';
    passwordInput.type = shouldShowPassword ? 'text' : 'password';
    passwordToggle.setAttribute('aria-label', shouldShowPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
});

const syncThemeControls = () => {
    const isDark = document.documentElement.classList.contains('dark');
    const themeToggles = document.querySelectorAll('[data-theme-toggle]');
    const themeIcons = document.querySelectorAll('[data-theme-icon]');

    themeToggles.forEach((button) => {
        button.setAttribute('aria-pressed', String(isDark));
        button.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
    });
    themeIcons.forEach((icon) => {
        icon.classList.toggle('hidden', icon.dataset.themeIcon === (isDark ? 'moon' : 'sun'));
    });
};

const applySavedTheme = () => {
    try {
        document.documentElement.classList.toggle('dark', localStorage.getItem('certifypro-theme') === 'dark');
    } catch (_) {
        // Keep the current theme if storage is unavailable.
    }
    syncThemeControls();
};

document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');
    if (!button) {
        return;
    }

    const isDark = !document.documentElement.classList.contains('dark');
    document.documentElement.classList.toggle('dark', isDark);
    try {
        localStorage.setItem('certifypro-theme', isDark ? 'dark' : 'light');
    } catch (_) {
        // Keep the selected theme for the current page when storage is unavailable.
    }
    syncThemeControls();
});

document.addEventListener('livewire:navigated', applySavedTheme);
applySavedTheme();

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm || 'Lanjutkan tindakan ini?')) {
            event.preventDefault();
        }
    });
});
