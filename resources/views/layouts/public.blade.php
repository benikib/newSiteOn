<!DOCTYPE html>
<html lang="fr">
<head>
    @if ((request()->is('/') || request()->is('produits') || request()->is('results') || request()->is('contact')) && app()->environment('production') && config('services.analytics.ga4_measurement_id'))
        @include('partials.analytics-consent')
    @endif
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bisika')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-color: #245c48; --primary-hover: #194532; --primary-dark: #173d2d; --text-dark: #202923; --text-light: #f8f9f6; }
        body { min-width: 320px; margin: 0; background: #f5f6f1; color: var(--text-dark); font-family: "Segoe UI", sans-serif; }
        .navbar { background: #203b30; box-shadow: 0 4px 18px rgba(20,35,27,.12); }
        .navbar-brand { color: #fff !important; }
        .navbar-brand img { width: 34px; height: 34px; margin-right: .65rem; border-radius: 50%; object-fit: cover; }
        .nav-link { min-height: 44px; display: flex; align-items: center; padding-inline: .85rem !important; color: #e4eee7 !important; }
        .nav-link:hover, .nav-link.active { color: #fff !important; }
        .nav-link.active { font-weight: 700; }
        .public-main { min-height: 66vh; }
        .public-footer { margin-top: 2rem; padding: 1.5rem 0; background: #203b30; color: #d6e3da; font-size: .86rem; }
        .public-footer-inner { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: .8rem; }
        .public-footer p { margin: 0; }
        .public-footer a { color: #fff; text-decoration-color: #91ab99; }
        .public-main .btn { min-height: 44px; }
        @media (max-width: 991.98px) { #navbarNav { padding: .65rem 0 1rem; } #navbarNav .ms-auto { margin-top: .6rem; } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { scroll-behavior: auto !important; animation-duration: .01ms !important; transition-duration: .01ms !important; } }
    </style>
    @yield('styles')
</head>
<body>
    @include('layouts.navigation')
    <main class="public-main">
        <div class="container pt-3"><x-flash-alert /></div>
        @yield('content')
    </main>
    <footer class="public-footer">
        <div class="container public-footer-inner">
            <p>© {{ now()->year }} BISIKA · Établissements, produits et services.</p>
            <a href="{{ route('contact') }}">Contacter BISIKA</a>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
