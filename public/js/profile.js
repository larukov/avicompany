document.addEventListener('DOMContentLoaded', function () {

    // Фильтрация бронирований по статусу
    var filterButtons = document.querySelectorAll('.booking-filter-btn');
    if (filterButtons.length) {
        filterButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                filterButtons.forEach(function (b) { b.classList.remove('active'); });
                this.classList.add('active');
                var filter = this.getAttribute('data-filter');
                document.querySelectorAll('.booking-item').forEach(function (item) {
                    var show = filter === 'all' || item.getAttribute('data-group') === filter;
                    item.style.display = show ? '' : 'none';
                });
            });
        });
    }
});