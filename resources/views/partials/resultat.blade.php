{{-- @extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2>Résultats de recherche</h2>

                @if (request()->anyFilled(['query', 'category', 'location', 'rating']))
                    <div class="search-filters mb-3">
                        <small class="text-muted">Filtres appliqués :</small>
                        @if (request('query'))
                            <span class="badge bg-primary me-2">
                                Mots-clés: {{ request('query') }}
                            </span>
                        @endif
                        @if (request('category'))
                            <span class="badge bg-primary me-2">
                                Catégorie: {{ ucfirst(request('category')) }}
                            </span>
                        @endif
                        @if (request('location'))
                            <span class="badge bg-primary me-2">
                                Localisation: {{ request('location') }}
                            </span>
                        @endif
                        @if (request('rating'))
                            <span class="badge bg-primary me-2">
                                Note min: {{ request('rating') }} étoiles
                            </span>
                        @endif
                        <a href="{{ route('search') }}" class="text-danger small">
                            <i class="fas fa-times"></i> Réinitialiser
                        </a>
                    </div>
                @endif

                @if ($results->isEmpty())
                    <div class="alert alert-warning">
                        Aucun résultat trouvé pour votre recherche.
                    </div>
                @else
                    <div class="row">
                        @foreach ($results as $etablissement)
                            <div class="col-md-4 mb-4">
                                @include('partials.etablissement-card', [
                                    'etablissement' => $etablissement,
                                ])
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $results->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection --}}
@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3 mb-0">
                        <i class="fas fa-search me-2 text-primary"></i>Résultats de recherche
                    </h2>
                    <span class="badge bg-primary rounded-pill">{{ $results->total() }} résultat(s)</span>
                </div>

                @if (request()->anyFilled(['query', 'category', 'location', 'rating']))
                    <div class="search-filters bg-light p-3 rounded mb-4">
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="me-2 text-muted small">
                                <i class="fas fa-filter me-1"></i>Filtres :
                            </span>

                            @if (request('query'))
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 me-2 mb-2">
                                    <i class="fas fa-keyword me-1"></i>{{ request('query') }}
                                </span>
                            @endif

                            @if (request('category'))
                                <span
                                    class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 me-2 mb-2">
                                    <i class="fas fa-tag me-1"></i>{{ ucfirst(request('category')) }}
                                </span>
                            @endif

                            @if (request('location'))
                                <span
                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 me-2 mb-2">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ request('location') }}
                                </span>
                            @endif

                            @if (request('rating'))
                                <span
                                    class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 me-2 mb-2">
                                    <i class="fas fa-star me-1"></i>{{ request('rating') }} étoiles+
                                </span>
                            @endif

                            <a href="{{ route('search') }}" class="small text-danger ms-auto mb-2">
                                <i class="fas fa-times-circle me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                @endif

                @if ($results->isEmpty())
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-search fa-3x text-muted mb-3"></i>
                            <h4 class="h5">Aucun résultat trouvé</h4>
                            <p class="text-muted mb-4">Essayez d'ajuster vos critères de recherche</p>
                            <a href="{{ route('search') }}" class="btn btn-primary">
                                <i class="fas fa-undo me-1"></i>Nouvelle recherche
                            </a>
                        </div>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach ($results as $etablissement)
                            <div class="col-md-6 col-lg-4">
                                @include('partials.etablissement-card', [
                                    'etablissement' => $etablissement,
                                    'show_category' => true,
                                    'show_rating' => true,
                                ])
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-center mt-5">
                        {{ $results->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
