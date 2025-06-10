<div class="card h-100 shadow-sm">
    @if ($etablissement->photos->count() > 0)
        <img src="{{ asset('storage/' . str_replace('public/', '', $etablissement->photos->first()->image_path)) }}"
            class="card-img-top" alt="{{ $etablissement->nom }}" style="height: 180px; object-fit: cover;">
    @else
        <div class="bg-secondary text-white text-center py-5" style="height: 180px;">
            <i class="fas fa-building fa-3x"></i>
        </div>
    @endif

    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <h5 class="card-title">{{ $etablissement->nom }}</h5>
            @if ($etablissement->note_moyenne)
                <span class="badge bg-warning text-dark">
                    <i class="fas fa-star"></i> {{ number_format($etablissement->note_moyenne, 1) }}
                </span>
            @endif
        </div>

        <p class="card-text text-muted small">
            <i class="fas fa-map-marker-alt text-primary"></i>
            {{ $etablissement->quartier }}, {{ $etablissement->ville }}
        </p>

        <p class="card-text">
            {{ Str::limit($etablissement->description, 100) }}
        </p>

        <div class="d-flex justify-content-between align-items-center">
            <span class="badge bg-primary">
                {{ ucfirst($etablissement->category) }}
            </span>
            <a href="{{ route('ets.info', $etablissement) }}" class="btn btn-sm btn-outline-primary">
                Voir plus <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</div>
