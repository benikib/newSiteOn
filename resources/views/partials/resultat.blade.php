<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bisika - Résultats de recherche</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #398FAA;
            --primary-hover: #2e7a91;
            --text-dark: #333333;
            --text-light: #f8f9fa;
            --shadow-sm: 0 2px 10px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--text-dark);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #2c3e50 100%);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .search-filters .badge {
            font-size: 0.8rem;
        }

        .no-results {
            text-align: center;
            padding: 3rem 0;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('assets/img/logo-ct.png') }}" alt="Bisika Logo" style="height: 30px; width: auto; margin-right: 10px; border-radius: 50%;">
                <span>Bisika</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">
                            <i class="fas fa-home me-1"></i> Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">
                            <i class="fas fa-envelope me-1"></i> Contact
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center gap-3 ms-auto">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-light px-4">
                            <i class="fas fa-sign-in-alt me-2"></i> Connexion
                        </a>
                    @else
                        <div class="dropdown">
                            <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                                <div class="d-flex align-items-center">
                                    @if (Auth::user()->avatar)
                                        <img src="{{ Auth::user()->avatar }}" alt="User Avatar" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                                    @else
                                        <div class="avatar-placeholder me-2">
                                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <span>{{ Auth::user()->name }}</span>
                                </div>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg">
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                        <i class="fas fa-user-circle me-2 text-primary"></i>Mon Profil
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item py-2" href="{{ route('dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2 text-info"></i>Tableau de bord
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h3 mb-0">
                        <i class="fas fa-search me-2 text-primary"></i>Résultats de recherche
                    </h2>
                    <span class="badge bg-primary rounded-pill">{{ $results->total() }} résultat(s)</span>
                </div>

                <!-- Filtres appliqués -->
                @if (request()->anyFilled(['query', 'category', 'location', 'rating']))
                    <div class="search-filters bg-light p-3 rounded mb-4">
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="me-2 text-muted small">
                                <i class="fas fa-filter me-1"></i>Filtres :
                            </span>

                            @if (request('query'))
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 me-2 mb-2">
                                    <i class="fas fa-keyword me-1"></i>{{ request('query') }}
                                </span>
                            @endif

                            @if (request('category'))
                                <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-10 me-2 mb-2">
                                    <i class="fas fa-tag me-1"></i>{{ ucfirst(request('category')) }}
                                </span>
                            @endif

                            @if (request('location'))
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 me-2 mb-2">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ request('location') }}
                                </span>
                            @endif

                            @if (request('rating'))
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 me-2 mb-2">
                                    <i class="fas fa-star me-1"></i>{{ request('rating') }} étoiles+
                                </span>
                            @endif

                            <a href="{{ route('search') }}" class="small text-danger ms-auto mb-2">
                                <i class="fas fa-times-circle me-1"></i>Réinitialiser
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Résultats -->
                @if ($results->isEmpty())
                    <div class="no-results">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="h5">Aucun résultat trouvé</h4>
                        <p class="text-muted mb-4">Essayez d'ajuster vos critères de recherche</p>
                        <a href="{{ route('search') }}" class="btn btn-primary">
                            <i class="fas fa-undo me-1"></i>Nouvelle recherche
                        </a>
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

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-5">
                        {{ $results->appends(request()->query())->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>