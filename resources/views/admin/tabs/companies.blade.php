<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <span class="fw-bold">Авиакомпании <small class="text-muted fw-normal">({{ $companies->count() }})</small></span>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#companyModal">+ Добавить
            компанию</button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table admin-table align-middle">
                <thead>
                    <tr>
                        <th>Логотип</th>
                        <th>Компания</th>
                        <th>Код IATA</th>
                        <th>Рейсов</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($companies as $company)
                        <tr>
                            <td>
                                @if ($company->logo_url)
                                    <img src="{{ $company->logo_url }}" alt="{{ $company->name }}"
                                        style="height: 36px; max-width: 110px; object-fit: contain;">
                                @else
                                    <span
                                        class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded fw-bold"
                                        style="width: 44px; height: 36px; font-size: .85rem;">
                                        {{ $company->initials }}
                                    </span>
                                @endif
                            </td>
                            <td class="fw-bold">{{ $company->name }}</td>
                            <td>{{ $company->iata_code ?: '—' }}</td>
                            <td>{{ $company->tickets_count }}</td>
                            <td>
                                <span class="badge {{ $company->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $company->is_active ? 'Активна' : 'Скрыта' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                        data-bs-target="#editCompanyModal{{ $company->id }}">✏️</button>
                                    <form action="{{ route('admin.companies.destroy', $company) }}" method="POST"
                                        onsubmit="return confirm('Удалить компанию {{ $company->name }}? Логотип пропадёт у её рейсов.');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Компаний пока нет</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
