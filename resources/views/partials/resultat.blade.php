{{-- <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .search-header {
            background-color: white;
            padding: 1.5rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .results-count {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .result-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .result-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .result-content {
            padding: 1.5rem;
        }

        .result-text {
            color: #495057;
            line-height: 1.6;
            margin-bottom: 0.5rem;
        }

        .pagination-info {
            color: #6c757d;
            text-align: center;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- En-tête de recherche -->
    <div class="search-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <form class="d-flex">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" placeholder="Rechercher..." value="Lorem ipsum">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="container py-4">
        <!-- Nombre de résultats -->
        <div class="results-count">
            <i class="fas fa-search me-2"></i>Nous avons trouvé deux résultats de votre recherche
        </div>

        <!-- Résultats -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- Premier résultat -->
                <div class="result-card">
                    <div class="result-content">
                        <h5 class="mb-3">Lorem ipsum dolor sit amet</h5>
                        <p class="result-text">Lorem ipsum dolor sit amet, consectetuer adipiscing elit.</p>
                        <p class="result-text">Marcenas porttitor congue massa. Fusce posuere, magna</p>
                        <a href="#" class="btn btn-outline-primary btn-sm mt-2">Voir plus</a>
                    </div>
                </div>

                <!-- Deuxième résultat -->
                <div class="result-card">
                    <div class="result-content">
                        <h5 class="mb-3">Lorem ipsum dolor sit amet</h5>
                        <p class="result-text">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                        <p class="result-text">Marcenas porttitor congue massa. Fusce posuere, magna</p>
                        <a href="#" class="btn btn-outline-primary btn-sm mt-2">Voir plus</a>
                    </div>
                </div>

                <!-- Information de pagination -->
                <div class="pagination-info">
                    Web 1920 – 8
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> --}}

@extends('layouts.app')

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
@endsection
