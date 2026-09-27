<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-info"
    id="sidenav-main">
    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            aria-hidden="true" id="iconSidenav"></i>
        <a class="navbar-brand m-0 d-flex align-items-center" href="{{ route('dashboard') }}">
            <img src="{{ asset('assets/img/logo-ct.png') }}" class="navbar-brand-img h-100" alt="Logo"
                style="border-radius: 50%;">
            <span class="ms-2 font-weight-bold fs-5 text-dark">Bisika</span>
        </a>
    </div>

    <hr class="horizontal dark mt-0 mb-2">

    <div class="collapse navbar-collapse w-auto h-auto" id="sidenav-collapse-main">
        <ul class="navbar-nav">

            <!-- Dashboard (visible uniquement à l'admin) -->
           
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
           <li class="nav-item">

                <a class="nav-link {{ request()->routeIs('dashboard')?'active':'' }}"
                    href="{{ route('admin.stocks.dashboard') }}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-primary text-center me-2">
                        <i class="fas fa-tachometer-alt text-white"></i>
                    </div>

                    <span class="nav-link-text">
                        Dashboard stock
                    </span>

                </a>

            </li>

            <!-- Section Gestion -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Gestion</h6>
            </li>

            <!-- Types d'établissements (admin uniquement) -->
            @if (Auth::user()->role === 'admin')
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
            @endif

            <!-- Établissements (visible pour tous) -->
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

            <!-- Publicités (visible pour tous) -->
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

            <!-- Gestion Utilisateurs (admin uniquement) -->
            @if (Auth::user()->role === 'admin')
                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Gestion Utilisateur</h6>
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

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admins.*') ? 'active' : '' }}"
                        href="{{ route('admins.index') }}">
                        <div
                            class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2 d-flex align-items-center justify-content-center">
                            <i class="fas fa-user-shield text-white"></i>
                        </div>
                        <span class="nav-link-text ms-1">Administrateurs</span>
                    </a>
                </li>
            @endif

            <!-- Mon compte -->
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

            <!-- Déconnexion -->
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

        </ul>
    </div>
</aside>
