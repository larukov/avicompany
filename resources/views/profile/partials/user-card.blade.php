<div class="card shadow-sm mb-4 border-0">
    <div class="card-body d-flex align-items-center">
        <div class="avatar-circle me-3">
            {{ mb_substr($user->lastname, 0, 1) }}{{ mb_substr($user->name, 0, 1) }}
        </div>
        <div>
            <h3 class="mb-1 fw-bold">{{ $user->lastname }} {{ $user->name }} {{ $user->patronomic }}</h3>
            <p class="text-muted mb-0">
                <span class="badge bg-success bg-opacity-10 border border-success">✓ Верифицирован</span>
                <span class="badge bg-warning bg-opacity-10 border border-warning ms-1">Premium</span>
            </p>
        </div>
    </div>
</div>