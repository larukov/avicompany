<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold">⚙️ Настройки</h5>
    </div>
    <div class="card-body">
        <h6 class="fw-bold mb-3"> Уведомления</h6>
        <div class="mb-4">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="notif-booking" checked>
                <label class="form-check-label" for="notif-booking">Подтверждение бронирования</label>
            </div>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="notif-checkin" checked>
                <label class="form-check-label" for="notif-checkin">Напоминание о регистрации (за 24 часа)</label>
            </div>
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="notif-promo">
                <label class="form-check-label" for="notif-promo">Спецпредложения и акции</label>
            </div>
        </div>

        <h6 class="fw-bold mb-3">🎯 Предпочтения</h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-bold">Язык интерфейса</label>
                <select class="form-select">
                    <option>Русский</option>
                    <option>English</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Валюта по умолчанию</label>
                <select class="form-select">
                    <option>Российский рубль (₽)</option>
                    <option>Доллар США ($)</option>
                </select>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 pt-3 border-top">
            <button class="btn btn-primary px-4">💾 Сохранить настройки</button>
        </div>

        <hr class="my-4">
        <div class="d-flex gap-2">
            <button class="btn btn-outline-danger">🗑️ Удалить аккаунт</button>
        </div>
    </div>
</div>