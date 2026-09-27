<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">

    <title>Bisika</title>

    <!-- Fonts - Optimisé avec preconnect et display=swap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 léger depuis CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Icônes avec fallback -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Dashboard CSS minimal -->
    <link href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.1.0') }}" rel="stylesheet">

    <!-- CSS Mobile Additionnel -->
    <style>
        /* Améliorations pour mobile */
        body {
            font-size: 0.9rem;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: transparent;
            touch-action: manipulation;
        }

        /* Navigation mobile améliorée */
        .navbar-mobile {
            padding: 0.5rem 1rem;
        }

        /* Boutons et éléments interactifs plus grands pour le tactile */
        .btn, .form-control, .form-select {
            min-height: 44px;
        }

        /* Espacement adaptatif */
        .main-content {
            padding: 0.5rem;
        }

        /* Menu latéral adaptatif */
        @media (max-width: 991.98px) {
            .aside-menu {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                position: fixed;
                top: 0;
                bottom: 0;
                z-index: 1040;
                overflow-y: auto;
                width: 75%;
                left: 0;
                background: white;
            }

            .aside-menu.show {
                transform: translateX(0);
            }

            .overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1039;
                display: none;
            }

            .overlay.show {
                display: block;
            }
        }

        /* Amélioration des alertes pour mobile */
        .alert {
            margin: 0.75rem;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }

        /* Optimisation des formulaires */
        input, select, textarea {
            font-size: 16px; /* Empêche le zoom sur iOS */
        }

        /* Amélioration performance scrolling */
        .main-content {
            -webkit-overflow-scrolling: touch;
        }

        /* Optimisation des images */
        img {
            max-width: 100%;
            height: auto;
        }
    </style>

    @yield('head')
</head>

<body class="bg-light">

    @include('layouts.etablissements.aside')

    <!-- Overlay pour menu mobile -->
    <div class="overlay" id="overlay"></div>

    <main class="main-content">
        @include('layouts.etablissements.nav')

        <!-- Alertes système -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert" id="errorAlert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
            </div>
        @endif

        <!-- Modal déconnexion -->
        <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la déconnexion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body">
                        Êtes-vous sûr de vouloir vous déconnecter ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-danger"
                            onclick="document.getElementById('logout-form').submit();">Se déconnecter</button>
                    </div>
                </div>
            </div>
        </div>

        @yield('content')
    </main>

    <!-- Core JS léger avec préchargement -->
    <link rel="preload" as="script" href="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js">
    <link rel="preload" as="script" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>

    <script>
        // Fermeture automatique des alertes (ES5 compatible)
        document.addEventListener('DOMContentLoaded', function() {
            var alerts = [document.getElementById('successAlert'), document.getElementById('errorAlert')];
            for (var i = 0; i < alerts.length; i++) {
                if (alerts[i]) {
                    (function(alertEl) {
                        setTimeout(function() {
                            try {
                                var bsAlert = new bootstrap.Alert(alertEl);
                                bsAlert.close();
                            } catch (e) {
                                console.warn('Erreur fermeture alerte:', e);
                            }
                        }, 4000); // 4 secondes pour mobile faible
                    })(alerts[i]);
                }
            }

            // Gestion du menu mobile
            var overlay = document.getElementById('overlay');
            if (overlay) {
                overlay.addEventListener('click', function() {
                    document.querySelector('.aside-menu').classList.remove('show');
                    this.classList.remove('show');
                });
            }
        });

        // Fonction pour toggle le menu mobile
        function toggleMobileMenu() {
            var menu = document.querySelector('.aside-menu');
            var overlay = document.getElementById('overlay');

            if (menu && overlay) {
                menu.classList.toggle('show');
                overlay.classList.toggle('show');
            }
        }
    </script>
    @yield('styles')
    
    @yield('scripts')
</body>

</html>
