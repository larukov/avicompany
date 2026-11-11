(function () {
    'use strict';

    function activate(sidebar, trigger) {
        var selector = trigger.getAttribute('data-bs-target') || trigger.getAttribute('href');
        var target = selector ? document.querySelector(selector) : null;
        var content = target ? target.closest('.tab-content') : null;

        sidebar.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (item) {
            var isActive = item === trigger;
            item.classList.toggle('active', isActive);
            item.setAttribute('aria-selected', isActive ? 'true' : 'false');

            // Держим классы бейджа в том же состоянии, что и при серверном рендере
            var badge = item.querySelector('.badge');
            if (badge) {
                badge.classList.toggle('bg-white', isActive);
                badge.classList.toggle('text-primary', isActive);
                badge.classList.toggle('bg-primary', !isActive);
                badge.classList.toggle('text-white', !isActive);
            }
        });

        if (!content) {
            return;
        }

        Array.prototype.forEach.call(content.children, function (pane) {
            if (!pane.classList.contains('tab-pane')) {
                return;
            }
            var isTarget = pane === target;
            pane.classList.toggle('active', isTarget);
            pane.classList.toggle('show', isTarget);
        });
    }

    // Переключаем вкладки сами: Bootstrap ищет активный пункт как `ul > li > .active`,
    // а у нас класс active висит на самом li, поэтому старый пункт оставался синим,
    // а повторный клик по нему игнорировался. Capture-фаза гасит обработчик Bootstrap.
    document.addEventListener('click', function (e) {
        if (!e.target || !e.target.closest) {
            return;
        }

        var trigger = e.target.closest('[data-sidebar] [data-bs-toggle="tab"]');
        if (!trigger) {
            return;
        }

        var sidebar = trigger.closest('[data-sidebar]');
        if (!sidebar) {
            return;
        }

        e.preventDefault();
        e.stopPropagation();

        activate(sidebar, trigger);

        var id = (trigger.getAttribute('data-bs-target') || '').replace(/^#/, '');
        if (id && window.history && window.history.replaceState) {
            window.history.replaceState(null, '', '#' + id);
        }
    }, true);

    document.addEventListener('DOMContentLoaded', function () {
        var sidebar = document.querySelector('[data-sidebar]');
        var hash = window.location.hash;
        if (!sidebar || !hash) {
            return;
        }

        var trigger = sidebar.querySelector('[data-bs-target="' + hash + '"]');
        if (trigger) {
            activate(sidebar, trigger);
        }
    });
})();
