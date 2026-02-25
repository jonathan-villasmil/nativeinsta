import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * apiFetch — a drop-in replacement for fetch() that automatically includes:
 *   - X-CSRF-TOKEN header (read from the <meta name="csrf-token"> tag)
 *   - X-Requested-With: XMLHttpRequest (so Laravel detects AJAX requests)
 *
 * Usage:  apiFetch('/some-url', { method: 'POST', body: ... })
 */
window.apiFetch = function (url, options = {}) {
    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const headers = {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': token,
        ...(options.headers ?? {}),
    };

    return fetch(url, { ...options, headers });
};
