document.addEventListener('DOMContentLoaded', function () {
    // ===== 2. Поиск по рейсам =====
    var flightSearch = document.getElementById('flightSearch');
    if (flightSearch) {
        flightSearch.addEventListener('input', function () {
            var q = this.value.toLowerCase();
            document.querySelectorAll('#flightsTableBody tr').forEach(function (row) {
                // Простая фильтрация текста строки
                row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
            });
        });
    }

    // ===== 3. Поиск по билетам =====
    var ticketSearch = document.getElementById('ticketSearch');
    if (ticketSearch) {
        ticketSearch.addEventListener('input', function () {
            var q = this.value.toLowerCase();
            document.querySelectorAll('#ticketsTableBody tr').forEach(function (row) {
                row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? '' : 'none';
            });
        });
    }
});