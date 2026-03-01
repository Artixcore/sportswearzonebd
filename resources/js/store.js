/**
 * Storefront JS: Bootstrap 5
 */
import 'bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        [...tooltipTriggerList].forEach(el => new bootstrap.Tooltip(el));
    }
});
