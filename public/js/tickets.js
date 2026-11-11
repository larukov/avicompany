document.addEventListener('DOMContentLoaded', function () {
    var sortSelect = document.querySelector('select[name="sort"]');
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            var url = new URL(window.location.href);
            url.searchParams.set('sort', this.value);
            window.location.href = url.toString();
        });
    }

    // Сайдбар фильтров: на десктопе раскрыт, на мобильных — скрыт и открывается по кнопке
    var collapseEl = document.getElementById('filtersCollapse');
    var toggleBtn = document.querySelector('[data-bs-target="#filtersCollapse"]');
    if (collapseEl && typeof bootstrap !== 'undefined' && bootstrap.Collapse) {
        try {
            var collapse = bootstrap.Collapse.getOrCreateInstance ?
                bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false }) :
                new bootstrap.Collapse(collapseEl, { toggle: false });

            function applyResponsive() {
                if (window.innerWidth >= 992) {
                    collapse.show();
                    if (toggleBtn) toggleBtn.classList.add('d-none');
                } else {
                    collapse.hide();
                    if (toggleBtn) toggleBtn.classList.remove('d-none');
                }
            }

            applyResponsive();
            var timer;
            window.addEventListener('resize', function () {
                clearTimeout(timer);
                timer = setTimeout(applyResponsive, 150);
            });
        } catch (e) {
            // если collapse не сработал — на десктопе просто показываем болеем
            if (window.innerWidth >= 992) collapseEl.classList.add('show');
        }
    } else if (collapseEl && window.innerWidth >= 992) {
        collapseEl.classList.add('show');
    }
});