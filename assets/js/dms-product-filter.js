/**
 * DMS Connect Product Filter
 *
 * Behaviors:
 *  - Mobile collapse/expand of the filter body via the header button.
 *  - Auto-submit when a checkbox is toggled (debounced) on desktop.
 *  - Form submit serializes filter groups to comma-separated query params
 *    so the resulting URL is short and shareable.
 */
(function () {
    'use strict';

    var MOBILE_BREAKPOINT = 768;

    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    function isMobile() {
        return window.matchMedia('(max-width: ' + MOBILE_BREAKPOINT + 'px)').matches;
    }

    function initSidebar(sidebar) {
        var form = sidebar.querySelector('.dms-cpf-form');
        var header = sidebar.querySelector('.dms-cpf-header');
        if (!form) {
            return;
        }

        // Collapsible header (mobile only). Default collapsed on mobile when enabled.
        if (header && sidebar.classList.contains('dms-cpf-collapsible')) {
            if (isMobile()) {
                sidebar.setAttribute('aria-collapsed', 'true');
                header.setAttribute('aria-expanded', 'false');
            } else {
                sidebar.setAttribute('aria-collapsed', 'false');
                header.setAttribute('aria-expanded', 'true');
            }

            header.addEventListener('click', function () {
                var collapsed = sidebar.getAttribute('aria-collapsed') === 'true';
                sidebar.setAttribute('aria-collapsed', collapsed ? 'false' : 'true');
                header.setAttribute('aria-expanded', collapsed ? 'true' : 'false');
            });

            window.addEventListener('resize', function () {
                if (!isMobile()) {
                    sidebar.setAttribute('aria-collapsed', 'false');
                    header.setAttribute('aria-expanded', 'true');
                }
            });
        }

        // Coalesce repeated checkbox name="dms_xxx[]" entries into a single
        // comma-separated query value to keep URLs concise.
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            navigateWithFilters(form);
        });

        // Auto-apply on desktop when a checkbox toggles.
        var debounce;
        form.addEventListener('change', function (e) {
            if (!e.target || e.target.type !== 'checkbox') return;
            if (isMobile()) return; // mobile users tap "Apply" explicitly
            clearTimeout(debounce);
            debounce = setTimeout(function () {
                navigateWithFilters(form);
            }, 350);
        });
    }

    function navigateWithFilters(form) {
        var action = form.getAttribute('action') || window.location.pathname;
        var url;
        try {
            url = new URL(action, window.location.origin);
        } catch (err) {
            url = new URL(window.location.href);
        }

        // Start fresh: drop existing dms_* params and replace with current selection.
        var params = url.searchParams;
        var keysToRemove = [];
        params.forEach(function (_, key) {
            if (key.indexOf('dms_') === 0) {
                keysToRemove.push(key);
            }
        });
        keysToRemove.forEach(function (k) { params.delete(k); });

        // Preserve any non-filter hidden inputs in the form.
        var hidden = form.querySelectorAll('input[type="hidden"]');
        hidden.forEach(function (input) {
            if (!input.name || input.name.indexOf('dms_') === 0) return;
            // Hidden inputs already mirror current $_GET, so they're already in the URL
            // when filtering the same page. Setting them again is harmless.
            params.set(input.name, input.value);
        });

        // Group checked checkboxes by their base name (strip trailing "[]").
        var grouped = {};
        var checkboxes = form.querySelectorAll('input[type="checkbox"]:checked');
        checkboxes.forEach(function (cb) {
            if (!cb.name) return;
            var key = cb.name.replace(/\[\]$/, '');
            if (key.indexOf('dms_') !== 0) return;
            if (!grouped[key]) grouped[key] = [];
            grouped[key].push(cb.value);
        });

        Object.keys(grouped).forEach(function (key) {
            params.set(key, grouped[key].join(','));
        });

        // Drop pagination when filters change.
        params.delete('paged');
        url.pathname = url.pathname.replace(/\/page\/\d+\/?$/, '/');

        window.location.href = url.toString();
    }

    ready(function () {
        var sidebars = document.querySelectorAll('.dms-cpf-sidebar');
        sidebars.forEach(initSidebar);
    });
})();
