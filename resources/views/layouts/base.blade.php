<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <!-- Favicons -->
  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png')}}">
  <link rel="icon" type="image/png" href="{{asset('assets/img/favicon.png')}}">
  
  <title>@yield('title', 'New Site')</title>
  
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Icons -->
  <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" onerror="this.onerror=null;this.href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css'">
  <link rel="stylesheet" href="{{ asset('assets/vendor/font-awesome/css/all.min.css') }}" onerror="this.onerror=null;this.href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'">
  
  <!-- Core CSS -->
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.1.0') }}" rel="stylesheet" />
  
  @yield('head')
</head>

<body class="g-sidenav-show bg-gray-100">
  @include('layouts.admins.aside')
  
  <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
    @include('layouts.admins.nav')
    
    <!-- Alertes système -->
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show mx-3 mt-3" role="alert" id="successAlert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
      </div>
    @endif
    
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show mx-3 mt-3" role="alert" id="errorAlert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
      </div>
    @endif

    @yield('content')
  </main>

  <!-- Core JS -->
  <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  
  <!-- ChartJS -->
  <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
  
  <!-- Main JS -->
  <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.1.0') }}"></script>
  
  <script>
    // Gestion des alertes
    document.addEventListener('DOMContentLoaded', function() {
      [document.getElementById('successAlert'), document.getElementById('errorAlert')]
        .filter(alert => alert)
        .forEach(alert => {
          setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
          }, 5000);
        });
    });
  </script>
  
  @yield('scripts')
</body>

</html>