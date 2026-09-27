<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3"
    id="sidenav-main"
    style="
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.85), rgba(37, 99, 235, 0.80)) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.18) !important;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.25) !important;
    ">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/img/logo-ct.png') }}" class="navbar-brand-img h-100" alt="Logo"
                style="border-radius: 50%; width: 40px; height: 40px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);">
            <span class="ms-2 font-weight-bold fs-5" style="color: #1a1a2e; text-shadow: 0 1px 4px rgba(255,255,255,0.2);">
                {{ config('app.name') }}
            </span>
        </a>
    </div>

    <hr class="horizontal dark mt-0 mb-2" style="border-color: rgba(0,0,0,0.08);">

    <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            <!-- ===== DASHBOARD ===== -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard_ets') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
                        style="background: rgba(255,255,255,0.20) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.10);">
                        <i class="fas fa-home" style="color: #1a1a2e;"></i>
                    </div>
                    <span class="nav-link-text" style="color: #1a1a2e; font-weight: 500;">Dashboard</span>
                </a>
            </li>

            <!-- ===== GESTION DE STOCK ===== -->
            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6" 
                    style="color: rgba(0,0,0,0.5) !important; letter-spacing: 1px;">
                    Gestion de stock
                </h6>
            </li>

            <!-- Point de vente -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('client.sales.pos') ? 'active' : '' }}"
                    href="{{ route('client.sales.pos') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
                        style="background: rgba(255,255,255,0.20) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.10);">
                        <i class="fas fa-cash-register" style="color: #1a1a2e;"></i>
                    </div>
                    <span class="nav-link-text" style="color: #1a1a2e; font-weight: 500;">Point de vente</span>
                </a>
            </li>

            <!-- Historique des ventes -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('client.sales.history') ? 'active' : '' }}"
                    href="{{ route('client.sales.history') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
                        style="background: rgba(255,255,255,0.20) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.10);">
                        <i class="fas fa-history" style="color: #1a1a2e;"></i>
                    </div>
                    <span class="nav-link-text" style="color: #1a1a2e; font-weight: 500;">Historique des ventes</span>
                </a>
            </li>

            <!-- ===== PRODUITS (DÉROULANT) ===== -->
            <li class="nav-item">
                <a class="nav-link" href="#productsSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('client.products.*') || request()->routeIs('client.categories.*') ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center w-100">
                        <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
                            style="background: rgba(255,255,255,0.20) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.10);">
                            <i class="fas fa-boxes" style="color: #1a1a2e;"></i>
                        </div>
                        <span class="nav-link-text flex-grow-1" style="color: #1a1a2e; font-weight: 500;">Produits</span>
                        <i class="fas fa-chevron-down" style="color: rgba(0,0,0,0.4); font-size: 0.65rem; transition: transform 0.3s ease;"></i>
                    </div>
                </a>
                <ul class="nav collapse {{ request()->routeIs('client.products.*') || request()->routeIs('client.categories.*') ? 'show' : '' }}" id="productsSubmenu" style="padding-left: 2.5rem;">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.products.index') ? 'active' : '' }}"
                            href="{{ route('client.products.index') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">📦 Liste des produits</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.products.create') ? 'active' : '' }}"
                            href="{{ route('client.products.create') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">➕ Ajouter un produit</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.categories.*') ? 'active' : '' }}"
                            href="{{ route('client.categories.index') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">🏷️ Catégories</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ===== STOCKS (DÉROULANT) ===== -->
            <li class="nav-item">
                <a class="nav-link" href="#stocksSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('client.stocks.*') ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center w-100">
                        <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
                            style="background: rgba(255,255,255,0.20) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.10);">
                            <i class="fas fa-warehouse" style="color: #1a1a2e;"></i>
                        </div>
                        <span class="nav-link-text flex-grow-1" style="color: #1a1a2e; font-weight: 500;">Stocks</span>
                        <i class="fas fa-chevron-down" style="color: rgba(0,0,0,0.4); font-size: 0.65rem; transition: transform 0.3s ease;"></i>
                    </div>
                </a>
                <ul class="nav collapse {{ request()->routeIs('client.stocks.*') ? 'show' : '' }}" id="stocksSubmenu" style="padding-left: 2.5rem;">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.stocks.index') ? 'active' : '' }}"
                            href="{{ route('client.stocks.index') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">📊 Vue d'ensemble</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.stocks.stock-in') ? 'active' : '' }}"
                            href="{{ route('client.stocks.stock-in') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">➕ Entrée de stock</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.stocks.stock-out') ? 'active' : '' }}"
                            href="{{ route('client.stocks.stock-out') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">➖ Sortie de stock</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.stocks.movements') ? 'active' : '' }}"
                            href="{{ route('client.stocks.movements') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">🔄 Mouvements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.stocks.alerts') ? 'active' : '' }}"
                            href="{{ route('client.stocks.alerts') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">🔔 Alertes stock</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.stocks.out-of-stock') ? 'active' : '' }}"
                            href="{{ route('client.stocks.out-of-stock') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">⚠️ Rupture de stock</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('client.stocks.valuation') ? 'active' : '' }}"
                            href="{{ route('client.stocks.valuation') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">📊 Valorisation</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ===== GESTION (DÉROULANT) ===== -->
            <li class="nav-item mt-2">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6" 
                    style="color: rgba(0,0,0,0.5) !important; letter-spacing: 1px;">
                    Gestion
                </h6>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#managementSubmenu" data-bs-toggle="collapse" role="button" aria-expanded="{{ request()->routeIs('users.etablissements.*') || request()->routeIs('etablissements.*') || request()->routeIs('personnels.*') || request()->routeIs('equipes.*') ? 'true' : 'false' }}">
                    <div class="d-flex align-items-center w-100">
                        <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
                            style="background: rgba(255,255,255,0.20) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.10);">
                            <i class="fas fa-cogs" style="color: #1a1a2e;"></i>
                        </div>
                        <span class="nav-link-text flex-grow-1" style="color: #1a1a2e; font-weight: 500;">Administration</span>
                        <i class="fas fa-chevron-down" style="color: rgba(0,0,0,0.4); font-size: 0.65rem; transition: transform 0.3s ease;"></i>
                    </div>
                </a>
                <ul class="nav collapse {{ request()->routeIs('users.etablissements.*') || request()->routeIs('etablissements.*') || request()->routeIs('personnels.*') || request()->routeIs('equipes.*') ? 'show' : '' }}" id="managementSubmenu" style="padding-left: 2.5rem;">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.etablissements.*') ? 'active' : '' }}"
                            href="{{ route('users.etablissements') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">🏢 Établissements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('etablissements.reservation.*') ? 'active' : '' }}"
                            href="{{ route('etablissements.reservation.index') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">📅 Réservations</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('etablissements.paiements.*') ? 'active' : '' }}"
                            href="{{ route('etablissements.paiements.index') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">💳 Paiements</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('etablissemensts.users.*') ? 'active' : '' }}"
                            href="{{ route('etablissemensts.users.index') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">👥 Utilisateurs</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('personnels.*') ? 'active' : '' }}"
                            href="{{ route('personnels.index') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">👤 Personnels</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('equipes.*') ? 'active' : '' }}"
                            href="{{ route('equipes.index') }}"
                            style="font-size: 0.8rem; padding: 0.3rem 0.8rem;">
                            <span class="nav-link-text" style="color: rgba(0,0,0,0.7);">👥 Équipes</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ===== DÉCONNEXION ===== -->
            <li class="nav-item mt-2">
                <hr class="horizontal dark mt-0 mb-2" style="border-color: rgba(0,0,0,0.08);">
            </li>

            <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center"
                        style="background: rgba(255,255,255,0.20) !important; backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.10);">
                        <i class="fas fa-sign-out-alt" style="color: #1a1a2e;"></i>
                    </div>
                    <span class="nav-link-text" style="color: #1a1a2e; font-weight: 500;">Déconnexion</span>
                </a>
            </li>

        </ul>
    </div>

    <!-- Footer -->
    <div class="sidenav-footer px-4 py-3">
        <div class="text-xs font-weight-bold text-uppercase opacity-6" style="color: rgba(0,0,0,0.4);">Version</div>
        <span class="text-xs" style="color: rgba(0,0,0,0.5);">{{ config('app.version', '1.0.0') }}</span>
    </div>

</aside>

<!-- Modal de déconnexion -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirmer la déconnexion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir vous déconnecter ?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt me-1"></i> Se déconnecter
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulaire caché pour la déconnexion -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>

<style>
    /* ===== SIDEBAR - FOND BLEU + GLASSMORPHISM ===== */
    .sidenav {
        width: 250px;
        transition: all 0.3s ease;
        z-index: 1030;
        height: calc(100vh - 2rem);
        margin: 1rem;
        overflow-y: auto;
        
        /* Fond bleu avec effet verre */
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.88), rgba(37, 99, 235, 0.82)) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 8px 32px rgba(37, 99, 235, 0.25) !important;
    }

    /* Supprimer le fond Bootstrap */
    .sidenav.bg-gradient-info {
        background: transparent !important;
    }

    /* Style des liens */
    .sidenav .nav-link {
        margin: 0.1rem 0.5rem;
        border-radius: 0.5rem;
        transition: all 0.3s ease;
        padding: 0.5rem 0.8rem;
        font-size: 0.875rem;
        color: rgba(0, 0, 0, 0.75) !important;
        position: relative;
    }

    /* Effet au survol */
    .sidenav .nav-link:hover {
        background: rgba(255, 255, 255, 0.15) !important;
        transform: translateX(4px);
        color: #000 !important;
    }

    /* État actif */
    .sidenav .nav-link.active {
        background: rgba(255, 255, 255, 0.22) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.20);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        color: #000 !important;
        font-weight: 600;
    }

    /* Indicateur actif */
    .sidenav .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 24px;
        background: linear-gradient(180deg, #ffffff, rgba(255,255,255,0.5));
        border-radius: 0 4px 4px 0;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
    }

    /* Icônes */
    .sidenav .icon-shape {
        width: 32px;
        height: 32px;
        transition: transform 0.3s ease, background 0.3s ease;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.15) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .sidenav .nav-link:hover .icon-shape {
        transform: scale(1.1) rotate(-3deg);
        background: rgba(255, 255, 255, 0.25) !important;
    }

    .sidenav .nav-link.active .icon-shape {
        background: rgba(255, 255, 255, 0.30) !important;
        box-shadow: 0 4px 20px rgba(255, 255, 255, 0.15);
    }

    /* Sous-menus */
    .sidenav .collapse .nav-link {
        font-size: 0.8rem !important;
        padding: 0.3rem 0.8rem !important;
        color: rgba(0, 0, 0, 0.6) !important;
    }

    .sidenav .collapse .nav-link:hover {
        color: #000 !important;
        background: rgba(255, 255, 255, 0.10) !important;
    }

    .sidenav .collapse .nav-link.active {
        color: #000 !important;
        background: rgba(255, 255, 255, 0.15) !important;
        font-weight: 600;
    }

    .sidenav .collapse .nav-link.active::before {
        display: none;
    }

    /* Icône chevron */
    .sidenav .fa-chevron-down {
        transition: transform 0.3s ease;
    }

    .sidenav .nav-link[aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }

    /* Scrollbar */
    .sidenav::-webkit-scrollbar {
        width: 4px;
    }

    .sidenav::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
    }

    .sidenav::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.25);
        border-radius: 10px;
    }

    .sidenav::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.40);
    }

    /* Texte */
    .sidenav .nav-link-text {
        color: rgba(0, 0, 0, 0.8) !important;
        font-weight: 500;
    }

    .sidenav .nav-link-text.text-secondary {
        color: rgba(0, 0, 0, 0.6) !important;
    }

    .sidenav-header .text-dark {
        color: #1a1a2e !important;
    }

    /* Titres de section */
    .sidenav .opacity-6 {
        color: rgba(0, 0, 0, 0.45) !important;
        letter-spacing: 1.5px;
        font-weight: 700;
        font-size: 0.65rem !important;
    }

    /* Séparateurs */
    .sidenav .horizontal.dark {
        border-color: rgba(0, 0, 0, 0.08) !important;
    }

    /* Footer */
    .sidenav-footer {
        border-top: 1px solid rgba(0, 0, 0, 0.06);
        margin-top: auto;
        padding: 0.75rem 1rem;
    }

    /* Responsive */
    @media (max-width: 1199.98px) {
        .sidenav {
            transform: translateX(-270px);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            margin: 0;
            border-radius: 0 !important;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.92), rgba(37, 99, 235, 0.88)) !important;
            backdrop-filter: blur(30px) !important;
            -webkit-backdrop-filter: blur(30px) !important;
        }

        .sidenav.show {
            transform: translateX(0);
            box-shadow: 0 0 40px rgba(37, 99, 235, 0.3);
        }
    }

    /* Supprimer les backgrounds indésirables */
    .bg-gradient-info {
        background: transparent !important;
    }

    .navbar-vertical {
        background: transparent !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconSidenav = document.getElementById('iconSidenav');
        const sidenav = document.getElementById('sidenav-main');

        // Toggle sidebar on mobile
        if (iconSidenav) {
            iconSidenav.addEventListener('click', function() {
                sidenav.classList.toggle('show');
            });
        }

        // Fermer le sidebar quand on clique à l'extérieur
        document.addEventListener('click', function(event) {
            const target = event.target;

            if (window.innerWidth < 1200 &&
                sidenav &&
                !sidenav.contains(target) &&
                target.id !== 'iconSidenav' &&
                sidenav.classList.contains('show')) {
                sidenav.classList.remove('show');
            }
        });

        // Fermer le sidebar après un clic sur un lien (mobile)
        if (window.innerWidth < 1200) {
            document.querySelectorAll('.nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (sidenav && sidenav.classList.contains('show')) {
                        sidenav.classList.remove('show');
                    }
                });
            });
        }

        // Gestion des sous-menus - garder ouvert si actif
        document.querySelectorAll('.nav-link[data-bs-toggle="collapse"]').forEach(function(link) {
            const target = document.querySelector(link.getAttribute('href'));
            if (target && target.classList.contains('show')) {
                link.setAttribute('aria-expanded', 'true');
            }
        });
    });
</script>