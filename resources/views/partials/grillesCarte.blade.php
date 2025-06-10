<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Grille de Cartes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary-color: #398FAA;
            --primary-hover: #2e7a91;
        }

        .navbar-custom {
            background-color: var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-nav .nav-link {
            color: white !important;
            font-weight: 500;
            margin-right: 15px;
            transition: all 0.3s;
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        .search-box {
            position: relative;
            width: 250px;
        }

        .search-box input {
            padding-right: 35px;
            border-radius: 20px;
            border: none;
        }

        .search-box i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-color);
        }

        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 180px;
            object-fit: cover;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: 20px;
            padding: 6px 20px;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        .page-title {
            color: var(--primary-color);
            margin-bottom: 30px;
            position: relative;
            padding-bottom: 10px;
        }

        .page-title:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 60px;
            height: 3px;
            background-color: var(--primary-color);
        }
    </style>
</head>

<body>

    <!-- Barre de navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="#">
                <i class="fas fa-globe me-2"></i>MonSite
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="#">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Emplacement</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contactez-nous</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Recherche</a></li>
                </ul>
                <form class="d-flex search-box me-3">
                    <input class="form-control me-2" type="search" placeholder="Rechercher..." aria-label="Recherche">
                    <i class="fas fa-search"></i>
                </form>
                <div class="d-flex">
                    <a href="#" class="btn btn-outline-light me-2">Connexion</a>
                    <a href="#" class="btn btn-light" style="color: var(--primary-color);">Inscription</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenu principal -->
    <main class="container my-5">
        <h1 class="text-center page-title">Nos Services</h1>

        <div class="row g-4">
            <!-- Exemple de carte - répéter pour chaque élément -->
            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <img src="https://source.unsplash.com/random/300x180/?technology" class="card-img-top"
                        alt="Technologie">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Service Technologie</h5>
                        <p class="card-text flex-grow-1">Solutions innovantes pour vos besoins technologiques.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="#" class="btn btn-primary">Détails</a>
                            <span class="badge bg-secondary">Nouveau</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <img src="https://source.unsplash.com/random/300x180/?business" class="card-img-top" alt="Business">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Conseil Business</h5>
                        <p class="card-text flex-grow-1">Accompagnement personnalisé pour développer votre entreprise.
                        </p>
                        <a href="#" class="btn btn-primary align-self-start">Détails</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <img src="https://source.unsplash.com/random/300x180/?health" class="card-img-top" alt="Santé">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Solutions Santé</h5>
                        <p class="card-text flex-grow-1">Services médicaux innovants et accessibles.</p>
                        <a href="#" class="btn btn-primary align-self-start">Détails</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-lg-3">
                <div class="card h-100">
                    <img src="https://source.unsplash.com/random/300x180/?education" class="card-img-top"
                        alt="Éducation">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Formations</h5>
                        <p class="card-text flex-grow-1">Programmes éducatifs adaptés à vos besoins.</p>
                        <a href="#" class="btn btn-primary align-self-start">Détails</a>
                    </div>
                </div>
            </div>

            <!-- Ajoutez d'autres cartes ici -->
        </div>

        <!-- Pagination -->
        <nav aria-label="Page navigation" class="mt-5">
            <ul class="pagination justify-content-center">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1">Précédent</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Suivant</a>
                </li>
            </ul>
        </nav>
    </main>

    <!-- Pied de page -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5 class="fw-bold">MonSite</h5>
                    <p>La meilleure solution pour vos besoins en ligne.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4 mb-md-0">
                    <h5 class="fw-bold">Liens</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Accueil</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Services</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-3 mb-4 mb-md-0">
                    <h5 class="fw-bold">Légal</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Mentions légales</a></li>
                        <li class="mb-2"><a href="#" class="text-white text-decoration-none">Confidentialité</a></li>
                        <li><a href="#" class="text-white text-decoration-none">CGU</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5 class="fw-bold">Newsletter</h5>
                    <p>Abonnez-vous pour nos dernières actualités.</p>
                    <form>
                        <div class="input-group mb-3">
                            <input type="email" class="form-control" placeholder="Votre email">
                            <button class="btn btn-primary" type="submit">OK</button>
                        </div>
                    </form>
                </div>
            </div>
            <hr class="my-4 bg-light">
            <div class="text-center">
                <p class="mb-0">&copy; 2023 MonSite. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>