<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-success"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/img/logo.png') }}" class="navbar-brand-img h-100" alt="Logo">
            <span class="ms-2 font-weight-bold fs-5 text-dark">Admin Panel</span>
        </a>
    </div>

    <hr class="horizontal dark mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">
            <!-- Dashboard -->
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                    href="{{ route('dashboard') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-primary text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-tachometer-alt text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Dashboard</span>
                </a>
            </li>

            <!-- Gestion des établissements -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Gestion</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('type_etablissements.*') ? 'active' : '' }}"
                    href="{{ route('type_etablissements.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-success text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-layer-group text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Types d'établissements</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('etablissements.*') ? 'active' : '' }}"
                    href="{{ route('etablissements.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-info text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-store-alt text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Établissements</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('publicites.*') ? 'active' : '' }}"
                    href="{{ route('publicites.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-warning text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-ad text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Publicités</span>
                </a>
            </li>

            <!-- Administration -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Administration</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}"
                    href="{{ route('users.index') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-users-cog text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Utilisateurs</span>
                </a>
            </li>

            <!-- Compte utilisateur -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Mon compte</h6>
            </li>

            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                    href="{{ route('profile.edit') }}">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-dark text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-user-edit text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Profil</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div
                        class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-secondary text-center me-2 d-flex align-items-center justify-content-center">
                        <i class="fas fa-sign-out-alt text-white"></i>
                    </div>
                    <span class="nav-link-text ms-1">Déconnexion</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>


</aside>

<style>
    .sidenav {
        width: 250px;
        transition: all 0.3s ease;
        z-index: 1035;
        box-shadow: 0 4px 20px 0 rgba(0, 0, 0, 0.05);
        height: calc(100vh - 2rem);
        margin: 1rem;
    }

    .sidenav-header {
        padding: 1.5rem 1rem 0.5rem;
    }

    .sidenav .nav-link {
        margin: 0.1rem 0.5rem;
        border-radius: 0.375rem;
        transition: all 0.2s ease;
        padding: 0.5rem 1rem;
    }

    .sidenav .nav-link.active {
        background-color: rgba(94, 114, 228, 0.1);
        font-weight: 600;
        color: #5e72e4;
    }

    .sidenav .nav-link.active .icon-shape {
        background-color: #5e72e4 !important;
    }

    .sidenav .nav-link:hover:not(.active) {
        background-color: rgba(0, 0, 0, 0.03);
    }

    .icon-shape {
        width: 36px;
        height: 36px;
        transition: all 0.2s ease;
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
            transform: translateX(-100%);
            position: fixed;
            left: 0;
            top: 0;
            margin: 0;
            height: 100vh;
        }

        .sidenav.show {
            transform: translateX(0);
        }

        .sidenav-header {
            padding-top: 1rem;
        }
    }
</style>

<script>
    // Toggle sidebar on mobile
    document.getElementById('iconSidenav').addEventListener('click', function() {
        document.getElementById('sidenav-main').classList.toggle('show');
    });

    // Close sidebar when clicking outside
    document.addEventListener('click', function(event) {
        const sidenav = document.getElementById('sidenav-main');
        const target = event.target;

        if (window.innerWidth < 1200 &&
            !target.closest('#sidenav-main') &&
            !target.closest('.navbar-toggler') &&
            sidenav.classList.contains('show')) {
            sidenav.classList.remove('show');
        }
    });

    // Active state management
    const currentPath = window.location.pathname;
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
        }
    });
</script>
