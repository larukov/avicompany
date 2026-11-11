<div class="card shadow-sm mb-4">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="mb-0 fw-bold">🛂 Документы
            <small class="text-muted fs-6 fw-normal">Сохранённые документы для быстрой покупки</small>
        </h5>
    </div>
    <div class="card-body">
        @if ($user->documents->isEmpty())
            <div class="text-center text-muted py-4">
                <i class="bi bi-file-earmark-text fs-1 d-block mb-3"></i>
                <p>У вас пока нет сохранённых документов</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                    <i class="bi bi-plus-circle me-2"></i>Добавить первый документ
                </button>
            </div>
        @else
            <div class="row g-3">
                @foreach ($user->documents as $doc)
                    <div class="col-md-6">
                        <div class="doc-card p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0 fw-bold">{{ $doc->type_name }}</h6>
                                <div class="d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-primary border-0" data-bs-toggle="modal"
                                        data-bs-target="#editDocumentModal{{ $doc->id }}">
                                        ✏️
                                    </button>
                                    <form action="{{ route('documents.destroy', $doc) }}" method="POST"
                                        class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0"
                                            onclick="return confirm('Удалить документ?')">🗑️</button>
                                    </form>
                                </div>
                            </div>
                            <p class="mb-1"><strong>№</strong> {{ $doc->series }} {{ $doc->number }}</p>
                            @if ($doc->issued_at)
                                <p class="mb-1"><strong>Выдан:</strong> {{ $doc->issued_at->format('d.m.Y') }}</p>
                            @endif
                            @if ($doc->expires_at)
                                <p class="mb-0">
                                    <strong>Действителен до:</strong> {{ $doc->expires_at->format('d.m.Y') }}
                                    @if ($doc->expires_at->isPast())
                                        <span class="badge bg-danger ms-2">Истёк</span>
                                    @endif
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach

                <div class="col-md-6">
                    <div class="doc-card p-3 d-flex align-items-center justify-content-center"
                        style="min-height: 140px; border-style: dashed !important; cursor: pointer;"
                        data-bs-toggle="modal" data-bs-target="#addDocumentModal">
                        <div class="text-center text-muted">
                            <div class="fs-2 mb-2">➕</div>
                            <span class="fw-bold">Добавить документ</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
