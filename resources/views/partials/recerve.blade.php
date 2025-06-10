@extends('layouts.app')
@section('content')
    <style>
        :root {
            --primary-color: #398FAA;
            --primary-hover: #2e7a91;
        }

        .navbar-custom {
            background-color: var(--primary-color);
        }

        .nav-link.active {
            font-weight: bold;
            border-bottom: 3px solid white;
        }

        .category-card {
            transition: all 0.3s;
            border-left: 4px solid var(--primary-color);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .budget-card {
            border-top: 3px solid var(--primary-color);
        }
    </style>
    </head>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-calendar-alt me-2"></i>Reservez
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Emplacement</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contactez-nous</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Recherche</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <div class="container my-5">
        <div class="row">
            <!-- Colonne des catégories -->
            <div class="col-md-8">
                <h2 class="mb-4"><i class="fas fa-list-ul me-2"></i>Nos catégories</h2>

                <div class="row g-4">
                    <!-- Restaurant -->
                    <div class="col-md-6">
                        <div class="card h-100 category-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-utensils me-2"></i>Restaurant
                                </h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Salle de Fête
                                        <span class="badge bg-primary rounded-pill">12</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Salle polyvalente
                                        <span class="badge bg-primary rounded-pill">8</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Hôtel
                                        <span class="badge bg-primary rounded-pill">15</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Site
                                        <span class="badge bg-primary rounded-pill">5</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Espace vert
                                        <span class="badge bg-primary rounded-pill">7</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Autres catégories (exemple) -->
                    <div class="col-md-6">
                        <div class="card h-100 category-card">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-building me-2"></i>Salles d'événements
                                </h5>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Salle de conférence
                                        <span class="badge bg-primary rounded-pill">10</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Salle de réunion
                                        <span class="badge bg-primary rounded-pill">14</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Espace coworking
                                        <span class="badge bg-primary rounded-pill">6</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne budget -->
            <div class="col-md-4 mt-4 mt-md-0">
                <div class="card budget-card h-100">
                    <div class="card-body">
                        <h3 class="card-title mb-4">
                            <i class="fas fa-euro-sign me-2"></i>Budget souhaité
                        </h3>

                        <form>
                            <div class="mb-3">
                                <label class="form-label">Secteur de votre choix</label>
                                <select class="form-select">
                                    <option>Paris</option>
                                    <option>Lyon</option>
                                    <option>Marseille</option>
                                    <option>Toulouse</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nombre de places</label>
                                <input type="number" class="form-control" placeholder="Ex: 50">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Budget approximatif</label>
                                <div class="input-group">
                                    <span class="input-group-text">€</span>
                                    <input type="number" class="form-control" placeholder="Ex: 1500">
                                </div>
                            </div>

                            <div class="d-grid">
                                <button class="btn btn-primary-custom btn-lg" type="submit">
                                    <i class="fas fa-search me-2"></i>Rechercher
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-calendar-alt me-2"></i>Reservez</h5>
                    <p class="mb-0">Trouvez la salle parfaite pour vos événements</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">&copy; 2023 Reservez. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@endsection
