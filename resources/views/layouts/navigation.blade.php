<nav class="navbar navbar-expand-lg navbar-dark sticky-top"
    style="background-color: var(--primary-color); box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <div class="container">
        <!-- Brand Logo with Animation -->
        <a class="navbar-brand fw-bold" href="{{ url('/') }}" style="transition: all 0.3s ease;">
            <i class="fas fa-building me-2"></i>MonSite
        </a>

        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ url('/') }}" aria-current="page">
                        <i class="fas fa-home me-1"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-map-marker-alt me-1"></i> Emplacement
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-envelope me-1"></i> Contactez-nous
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-search me-1"></i> Recherche
                    </a>
                </li>
            </ul>

            <!-- Auth Buttons -->
            <div class="d-flex align-items-center gap-2">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-light me-2">
                        <i class="fas fa-sign-in-alt me-1"></i> Connexion
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-light text-primary fw-medium">
                            <i class="fas fa-user-plus me-1"></i> Inscription
                        </a>
                    @endif
                @else
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" type="button"
                            id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="me-2 d-flex align-items-center">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="User Avatar" class="rounded-circle me-2"
                                        style="width: 30px; height: 30px; object-fit: cover;">
                                @else
                                    <i class="fas fa-user-circle me-1"></i>
                                @endif
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user me-2 text-muted"></i>Profil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cog me-2 text-muted"></i>Paramètres
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="{{ route('logout') }}"
                                    onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
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
