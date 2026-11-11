(function () {
    'use strict';

    function onReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    function initReturnDate(form) {
        var roundRadio = form.querySelector('input[name="trip_type"][value="round"]');
        var onewayRadio = form.querySelector('input[name="trip_type"][value="oneway"]');
        var wrap = form.querySelector('.js-return-date');
        var returnInput = form.querySelector('input[name="return_date"]');
        var dateInput = form.querySelector('input[name="date"]');

        if (!wrap || !returnInput) {
            return;
        }

        function syncTripType() {
            var isOneway = onewayRadio && onewayRadio.checked;
            wrap.classList.toggle('d-none', isOneway);
            returnInput.disabled = isOneway;
            if (isOneway) {
                returnInput.value = '';
            }
        }

        function syncDates() {
            var today = new Date().toISOString().slice(0, 10);
            if (dateInput) {
                dateInput.min = today;
            }
            if (dateInput && dateInput.value) {
                returnInput.min = dateInput.value;
                if (returnInput.value && returnInput.value < dateInput.value) {
                    returnInput.value = dateInput.value;
                }
            }
        }

        if (roundRadio) roundRadio.addEventListener('change', syncTripType);
        if (onewayRadio) onewayRadio.addEventListener('change', syncTripType);
        if (dateInput) dateInput.addEventListener('change', syncDates);
        syncTripType();
        syncDates();
    }

    function initCityAutocomplete(input) {
        var wrap = input.parentElement;
        var list = wrap.querySelector('[data-city-list]');
        if (!list) return;

        var url = input.getAttribute('data-url') || '/cities/suggest';
        var timer = null;
        var controller = null;
        var activeIndex = -1;
        var items = [];

        function hide() {
            list.hidden = true;
            list.innerHTML = '';
            activeIndex = -1;
            items = [];
        }

        function choose(value) {
            input.value = value;
            input.dispatchEvent(new Event('input', { bubbles: true }));
            hide();
        }

        function highlight() {
            items.forEach(function (item, i) {
                item.classList.toggle('active', i === activeIndex);
            });
        }

        function render(cities) {
            list.innerHTML = '';
            if (!cities.length) {
                hide();
                return;
            }

            items = cities.map(function (city) {
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action py-2';
                item.textContent = city;
                item.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    choose(city);
                });
                list.appendChild(item);
                return item;
            });

            activeIndex = -1;
            list.hidden = false;
        }

        function load(query) {
            if (controller) controller.abort();
            controller = new AbortController();

            fetch(url + '?q=' + encodeURIComponent(query), {
                headers: { 'Accept': 'application/json' },
                signal: controller.signal
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('bad response');
                    return response.json();
                })
                .then(function (cities) {
                    if (document.activeElement === input) {
                        render(Array.isArray(cities) ? cities : []);
                    }
                })
                .catch(function () { /* запрос отменён или сеть недоступна */ });
        }

        input.addEventListener('input', function () {
            var value = input.value.trim();
            if (timer) clearTimeout(timer);
            if (!value) {
                hide();
                return;
            }
            timer = setTimeout(function () { load(value); }, 200);
        });

        input.addEventListener('keydown', function (e) {
            if (list.hidden) return;

            if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                e.preventDefault();
                var step = e.key === 'ArrowDown' ? 1 : -1;
                activeIndex = (activeIndex + step + items.length) % items.length;
                highlight();
            } else if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                choose(items[activeIndex].textContent);
            } else if (e.key === 'Escape') {
                hide();
            }
        });

        input.addEventListener('blur', function () {
            setTimeout(hide, 150);
        });
    }

    function initDestinationButtons(form) {
        var buttons = document.querySelectorAll('[data-destination]');
        if (!buttons.length) return;

        var toInput = form.querySelector('input[name="to"]');
        var dateInput = form.querySelector('input[name="date"]');
        var section = document.getElementById('flight-search');

        buttons.forEach(function (button) {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                if (toInput) {
                    toInput.value = button.getAttribute('data-destination') || '';
                    toInput.focus();
                }
                if (section) {
                    section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
                if (dateInput) {
                    dateInput.focus({ preventScroll: true });
                }
            });
        });
    }

    onReady(function () {
        var form = document.getElementById('flightSearchForm');
        if (!form) return;

        initReturnDate(form);
        initDestinationButtons(form);
        form.querySelectorAll('[data-city-autocomplete]').forEach(initCityAutocomplete);
    });
})();
