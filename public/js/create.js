(function () {
    var cfg = window.bookingConfig || {};
    var currentStep = 1;
    var basePrice = Number(cfg.basePrice || 0);
    var passengersCount = Number(cfg.passengersCount || 1);
    var userMiles = Number(cfg.userMiles || 0);
    var myPassengerData = cfg.myPassengerData || {};

    function showStep(step) {
        var cur = document.getElementById('step' + currentStep);
        if (cur) cur.classList.remove('active');
        var curStep = document.querySelector('.step[data-step="' + currentStep + '"]');
        if (curStep) curStep.classList.remove('active');

        currentStep = step;
        var next = document.getElementById('step' + currentStep);
        if (next) next.classList.add('active');
        var nextStepEl = document.querySelector('.step[data-step="' + currentStep + '"]');
        if (nextStepEl) nextStepEl.classList.add('active');
        window.scrollTo(0, 0);
    }

    function markStepCompleted(step) {
        var el = document.querySelector('.step[data-step="' + step + '"]');
        if (el) el.classList.add('completed');
    }

    function validateStep(step) {
        var content = document.getElementById('step' + step);
        if (!content) return true;
        var firstInvalid = null;

        var cardMethod = content.querySelector('input[name="payment_method"]:checked');
        var isCard = cardMethod ? cardMethod.value === 'card' : false;
        var cardWrap = document.getElementById('cardFields');

        content.querySelectorAll('input, select, textarea').forEach(function (field) {
            var block = field.closest('.passenger-block');
            if (block && block.classList.contains('d-none')) {
                field.setCustomValidity('');
                return;
            }
            // Поля карты валидируем только при выбранном способе «Банковская карта»
            if (cardWrap && cardWrap.contains(field) && !isCard) {
                field.setCustomValidity('');
                return;
            }
            var parent = field.closest('.mb-3');
            if (parent) parent.classList.remove('has-invalid');
            if (field.hasAttribute('required') && !field.value.trim()) {
                field.setCustomValidity('Заполните это поле');
                if (parent) parent.classList.add('has-invalid');
                if (!firstInvalid) firstInvalid = field;
            } else {
                field.setCustomValidity('');
            }
        });

        if (firstInvalid) {
            firstInvalid.focus();
            return false;
        }
        return true;
    }

    function nextStep(step) {
        if (!validateStep(currentStep)) return;
        markStepCompleted(currentStep);
        showStep(step);
    }

    function prevStep(step) {
        var el = document.querySelector('.step[data-step="' + currentStep + '"]');
        if (el) el.classList.remove('completed');
        showStep(step);
    }

    function selectClass(classType, price) {
        document.querySelectorAll('.class-card:not(.passenger-count-card)').forEach(function (card) {
            card.classList.remove('selected');
        });
        if (window.event && window.event.currentTarget) {
            window.event.currentTarget.classList.add('selected');
        }
        document.getElementById('selectedClass').value = classType;
        document.getElementById('basePrice').value = price;
        basePrice = Number(price);
        document.getElementById('summaryClass').textContent = classType.charAt(0).toUpperCase() + classType.slice(1);
        updateTotal();
    }

    function selectPassengers(count) {
        passengersCount = count;
        document.getElementById('passengersCount').value = count;

        document.querySelectorAll('.passenger-count-card').forEach(function (card) {
            card.classList.toggle('selected', parseInt(card.dataset.count, 10) === count);
        });

        document.querySelectorAll('.passenger-block').forEach(function (block) {
            var idx = parseInt(block.dataset.passenger, 10);
            var hidden = idx >= count;
            block.classList.toggle('d-none', hidden);
            block.querySelectorAll('input, select').forEach(function (field) {
                field.disabled = hidden;
            });
        });

        document.getElementById('summaryPassengers').textContent = count + ' чел.';
        updateTotal();
    }

    function updateTotal() {
        var servicesTotal = 0;
        document.querySelectorAll('input[name="services[]"]:checked').forEach(function (checkbox) {
            var prices = {
                'extra_baggage': 1500,
                'seat_selection': 800,
                'special_meal': 600,
                'insurance': 500,
                'priority_boarding': 1200,
                'business_lounge': 2000
            };
            servicesTotal += prices[checkbox.value] * passengersCount;
        });

        var baseTotal = basePrice * passengersCount;
        var subtotal = baseTotal + servicesTotal;

        var milesCheck = document.getElementById('useMilesCheck');
        var milesUsed = 0;
        if (milesCheck && milesCheck.checked) {
            var milesInput = document.getElementById('miles_amount');
            var val = parseInt(milesInput.value || 0, 10);
            if (val > userMiles) val = userMiles;
            if (val > subtotal) val = subtotal;
            milesInput.value = val;
            milesUsed = val;
        }

        var total = Math.max(0, subtotal - milesUsed);

        document.getElementById('summaryBase').textContent = baseTotal.toLocaleString('ru-RU') + ' ₽';
        document.getElementById('summaryServices').textContent = servicesTotal.toLocaleString('ru-RU') + ' ₽';
        var milesRow = document.getElementById('summaryMilesRow');
        if (milesRow) {
            if (milesUsed > 0) {
                milesRow.style.display = '';
                document.getElementById('summaryMilesDiscount').textContent = '−' + milesUsed.toLocaleString('ru-RU') + ' ₽';
            } else {
                milesRow.style.display = 'none';
            }
        }
        document.getElementById('summaryTotal').textContent = total.toLocaleString('ru-RU') + ' ₽';
    }

    function fillPassengerWithMyData() {
        var block = document.querySelector('.passenger-block:not(.d-none)');
        if (!block) return;

        Object.keys(myPassengerData).forEach(function (key) {
            var value = myPassengerData[key];
            if (value === null || value === undefined || value === '') return;
            var el = block.querySelector('[name="passengers[0][' + key + ']"]');
            if (el) el.value = value;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Начальное состояние: отключаем скрытые блоки пассажиров
        document.querySelectorAll('.passenger-block').forEach(function (block) {
            if (block.classList.contains('d-none')) {
                block.querySelectorAll('input, select').forEach(function (field) {
                    field.disabled = true;
                });
            }
        });

        // Выбор числа пассажиров
        document.querySelectorAll('.passenger-count-card').forEach(function (card) {
            card.addEventListener('click', function () {
                selectPassengers(parseInt(this.dataset.count, 10));
            });
        });

        // Пересчёт при выборе доп. услуг
        document.querySelectorAll('input[name="services[]"]').forEach(function (checkbox) {
            checkbox.addEventListener('change', updateTotal);
        });

        // Переключение полей карты по способу оплаты
        document.querySelectorAll('input[name="payment_method"]').forEach(function (radio) {
            radio.addEventListener('change', function () {
                document.getElementById('cardFields').style.display = this.value === 'card' ? 'block' : 'none';
            });
        });

        // Валидация последнего шага при отправке
        document.getElementById('bookingForm').addEventListener('submit', function (e) {
            if (!validateStep(currentStep)) {
                e.preventDefault();
                return;
            }
            var btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = '⏳ Обработка...';
            }
        });

        // Оплата милями
        var useMilesCheck = document.getElementById('useMilesCheck');
        if (useMilesCheck) {
            useMilesCheck.addEventListener('change', function () {
                document.getElementById('milesAmountWrap').classList.toggle('d-none', !this.checked);
                if (!this.checked) {
                    var mi = document.getElementById('miles_amount');
                    if (mi) mi.value = 0;
                }
                updateTotal();
            });
        }

        var milesAmount = document.getElementById('miles_amount');
        if (milesAmount) {
            milesAmount.addEventListener('input', updateTotal);
        }
    });

    document.addEventListener('click', function (e) {
        var btn = e.target.closest('#fillMyData');
        if (btn) fillPassengerWithMyData();
    });

    // Экспорт для inline-обработчиков onclick в шаблоне
    window.nextStep = nextStep;
    window.prevStep = prevStep;
    window.selectClass = selectClass;
})();