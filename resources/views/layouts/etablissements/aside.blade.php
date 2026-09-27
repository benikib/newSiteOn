<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3"
    id="sidenav-main">
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
                        style="background: #f1f5fa !important; border: 1px solid #e7edf4;">
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
                        style="background: #f1f5fa !important; border: 1px solid #e7edf4;">
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
                        style="background: #f1f5fa !important; border: 1px solid #e7edf4;">
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
                            style="background: #f1f5fa !important; border: 1px solid #e7edf4;">
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
                            style="background: #f1f5fa !important; border: 1px solid #e7edf4;">
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
                            style="background: #f1f5fa !important; border: 1px solid #e7edf4;">
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
                        style="background: #f1f5fa !important; border: 1px solid #e7edf4;">
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
        <div class="modal-content" style="background: #fff;">
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
    /* Sidebar */
    .sidenav {
        width: 250px;
        z-index: 1040;
        height: calc(100vh - 2rem);
        margin: 1rem;
        overflow-y: auto;
        background: #fff !important;
        border: 1px solid #e7edf4 !important;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.08) !important;
    }

    .sidenav .nav-link {
        margin: 0.1rem 0.65rem;
        border-radius: 0.45rem;
        transition: background-color 0.15s ease, color 0.15s ease;
        padding: 0.55rem 0.75rem;
        font-size: 0.875rem;
        color: #344054 !important;
        position: relative;
    }

    .sidenav .nav-link:hover {
        background: #f3f6fa !important;
        color: #1d2939 !important;
    }

    .sidenav .nav-link.active {
        background: #eaf1fb !important;
        color: #2459a6 !important;
        font-weight: 600;
    }

    .sidenav .nav-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 20px;
        background: #3973c6;
        border-radius: 0 3px 3px 0;
    }

    .sidenav .icon-shape {
        width: 34px;
        height: 34px;
        transition: background-color 0.15s ease;
        flex-shrink: 0;
        background: #f1f5fa !important;
        border: 1px solid #e7edf4;
    }

    .sidenav .nav-link:hover .icon-shape {
        background: #e8eef7 !important;
    }

    .sidenav .nav-link.active .icon-shape {
        background: #dce8f8 !important;
        border-color: #dce8f8;
    }

    .sidenav .collapse .nav-link {
        font-size: 0.8rem !important;
        padding: 0.3rem 0.8rem !important;
        color: #596579 !important;
    }

    .sidenav .collapse .nav-link:hover {
        color: #1d2939 !important;
        background: #f3f6fa !important;
    }

    .sidenav .collapse .nav-link.active {
        color: #2459a6 !important;
        background: #eaf1fb !important;
        font-weight: 600;
    }

    .sidenav .collapse .nav-link.active::before {
        display: none;
    }

    .sidenav .fa-chevron-down {
        transition: transform 0.15s ease;
    }

    .sidenav .nav-link[aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }

    .sidenav::-webkit-scrollbar {
        width: 5px;
    }

    .sidenav::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 5px;
    }

    .sidenav .nav-link-text {
        color: #344054 !important;
        font-weight: 500;
    }

    .sidenav .nav-link-text.text-secondary {
        color: #667085 !important;
    }

    .sidenav-header .text-dark {
        color: #1d2939 !important;
    }

    .sidenav .opacity-6 {
        color: #667085 !important;
        font-weight: 700;
        font-size: 0.65rem !important;
    }

    .sidenav .horizontal.dark {
        border-color: #e7edf4 !important;
    }

    .sidenav-footer {
        border-top: 1px solid #e7edf4;
        margin-top: auto;
        padding: 0.75rem 1rem;
    }

    @media (max-width: 1199.98px) {
        .sidenav {
            transform: translateX(-100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: min(280px, 85vw);
            margin: 0;
            border-radius: 0 !important;
            transition: transform 0.18s ease-out;
            will-change: transform;
        }

        .sidenav.show {
            transform: translateX(0);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.14) !important;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .sidenav,
        .sidenav .nav-link,
        .sidenav .icon-shape,
        .sidenav .fa-chevron-down {
            transition: none !important;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const iconSidenav = document.getElementById('iconNavbarSidenav') || document.getElementById('iconSidenav');
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
                !(iconSidenav && iconSidenav.contains(target)) &&
                sidenav.classList.contains('show')) {
                sidenav.classList.remove('show');
            }
        });

        // Fermer le sidebar après un clic sur un lien (mobile)
        if (window.innerWidth < 1200) {
            document.querySelectorAll('.nav-link').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (link.getAttribute('data-bs-toggle') !== 'collapse' &&
                        sidenav && sidenav.classList.contains('show')) {
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