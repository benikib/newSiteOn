<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-info"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/img/logo-ct.png') }}" class="navbar-brand-img h-100" alt="Logo">
            <span class="ms-2 font-weight-bold fs-5 text-dark">{{ config('app.name') }}</span>
        </a>
    </div>

    <hr class="horizontal dark mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard_ets') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-home text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <!-- Établissement -->
            <li class="nav-item">
                <a class="nav-link" href="{{ route('users.etablissements') }}"
                    class="{{ request()->routeIs('users.etablissements.*') ? 'active' : '' }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-store text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Établissements</span>
                </a>
            </li>
            <!-- Réservations -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('etablissements.reservation.*') ? 'active' : '' }}"
                    href="{{ route('etablissements.reservation.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users-cog text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Reservation</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('etablissements.paiements.*') ? 'active' : '' }}"
                    href="{{ route('etablissements.paiements.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users-cog text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Paiement</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('etablissemensts.users.*') ? 'active' : '' }}"
                    href="{{ route('etablissemensts.users.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users-cog text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Utilisateurs</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('personnels.*') ? 'active' : '' }}"
                    href="{{ route('personnels.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users-cog text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Personnels</span>
                </a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="{{ route('users.promotions') }}"
                    class="{{ request()->routeIs('users.promotions.*') ? 'active' : '' }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-store text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Promotions</span>
                </a>
            </li> --}}

            <li class="nav-item">
                <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-secondary text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sign-out-alt text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Déconnexion</span>
                </a>

                <!-- Formulaire caché -->
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>

            {{-- <!-- Publicité -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('etablissements.publicites.*') ? 'active' : '' }}" href="{{ route('etablissements.publicites.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-warning text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-ad text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Publicité</span>
                </a>
            </li>

            <!-- Promotions -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('etablissements.promotions.*') ? 'active' : '' }}" href="{{ route('etablissements.promotions.index') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-tag text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Promotions</span>
                </a>
            </li>
 --}}
            <!-- Section Mon compte -->
            {{-- <li class="nav-item mt-4">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Mon compte</h6>
            </li>

            <!-- Profile -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.show') }}">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-dark text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profil</span>
                </a>
            </li>

            <!-- Déconnexion -->
            <li class="nav-item">
                <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-secondary text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sign-out-alt text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Déconnexion</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div> --}}

            <!-- Footer optionnel -->
            {{-- <div class="sidenav-footer px-4 py-3">
                <div class="text-xs font-weight-bold text-uppercase opacity-6">Version</div>
                <span class="text-xs">{{ config('app.version', '1.0.0') }}</span>
            </div> --}}
</aside>

<style>
    .sidenav {
        width: 250px;
        transition: all 0.3s ease;
        z-index: 1030;
        box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.07);
        height: calc(100vh - 2rem);
        margin: 1rem;
    }

    .sidenav .nav-link {
        margin: 0.2rem 0.5rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
    }

    .sidenav .nav-link.active {
        background-color: rgba(0, 0, 0, 0.05);
        font-weight: 600;
        color: var(--bs-primary);
    }

    .sidenav .nav-link:hover:not(.active) {
        background-color: rgba(0, 0, 0, 0.03);
    }

    .icon-shape {
        width: 32px;
        height: 32px;
        transition: transform 0.2s ease;
    }

    .nav-link:hover .icon-shape {
        transform: scale(1.1);
    }

    .sidenav-footer {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    @media (max-width: 1199.98px) {
        .sidenav {
            transform: translateX(-270px);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            margin: 0;
        }

        .sidenav.show {
            transform: translateX(0);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
    }
</style>

<script>
    // Toggle sidebar on mobile
    document.getElementById('iconSidenav').addEventListener('click', function() {
        document.getElementById('sidenav-main').classList.toggle('show');
    });

    // Fermer le sidebar quand on clique à l'extérieur
    document.addEventListener('click', function(event) {
        const sidenav = document.getElementById('sidenav-main');
        const target = event.target;

        if (window.innerWidth < 1200 &&
            !sidenav.contains(target) &&
            target.id !== 'iconSidenav' &&
            sidenav.classList.contains('show')) {
            sidenav.classList.remove('show');
        }
    });
</script>
