<nav class="navbar navbar-expand-lg navbar-dark sticky-top"
    style="background: linear-gradient(135deg, var(--primary-color) 0%, #2c3e50 100%); box-shadow: 0 4px 20px rgba(0,0,0,0.15);">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}"
            style="transition: all 0.3s ease;">
            <img src="{{ asset('assets/img/logo-ct.png') }}" alt="Bisika Logo"
                style="height: 30px; width: auto; margin-right: 10px; border-radius: 50%;">
            <span style="font-family: 'Poppins', sans-serif; font-weight: 600;">Bisika</span>
        </a>
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation Links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item mx-1">
                    <a class="nav-link position-relative" href="{{ url('/') }}">
                        <i class="fas fa-home me-1"></i> Accueil
                        <span class="nav-link-underline"></span>
                    </a>
                </li>
                <li class="nav-item mx-1">
                    <a class="nav-link position-relative" href="{{ route('contact') }}">
                        <i class="fas fa-envelope me-1"></i> Contact
                        <span class="nav-link-underline"></span>
                    </a>
                </li>
            </ul>

            <!-- Auth Buttons -->
            <div class="d-flex align-items-center gap-3 ms-auto">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-hover-glow px-4">
                        <i class="fas fa-sign-in-alt me-2"></i> Connexion
                    </a>
                    {{-- @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-light btn-hover-glow text-primary px-4">
                            <i class="fas fa-user-plus me-2"></i> Inscription
                        </a>
                    @endif --}}
                @else
                    <!-- User Dropdown -->
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center btn-hover-glow"
                            type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" alt="User Avatar" class="rounded-circle me-2"
                                        style="width: 32px; height: 32px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);">
                                @else
                                    <div class="avatar-placeholder me-2">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span>{{ Auth::user()->name }}</span>
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="userDropdown"
                            style="border: none; min-width: 220px;">
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
                            <li>
                                <hr class="dropdown-divider my-1">
                            </li>
                            <li>
                                <a class="dropdown-item py-2 text-danger" href="{{ route('logout') }}"
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

<style>
    /* Styles personnalisés */
    .gradient-text {
        background: linear-gradient(90deg, #ffffff 0%, #e6f7ff 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        font-weight: 700;
    }

    .nav-link {
        position: relative;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }

    .nav-link-underline {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: rgba(255, 255, 255, 0.7);
        transition: width 0.3s ease;
    }

    .nav-link:hover .nav-link-underline {
        width: 70%;
    }

    .btn-hover-glow {
        transition: all 0.3s ease;
    }

    .btn-hover-glow:hover {
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
        transform: translateY(-1px);
    }

    .avatar-placeholder {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .dropdown-menu {
        border-radius: 10px;
        overflow: hidden;
    }

    .dropdown-item {
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: linear-gradient(90deg, rgba(52, 152, 219, 0.1) 0%, transparent 100%);
        padding-left: 1.5rem;
    }
</style>
