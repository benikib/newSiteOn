<!-- Navbar -->


<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur"
    navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        <!-- Bouton mobile pour ouvrir le menu (visible uniquement en dessous de xl) -->
        <button type="button" id="iconNavbarSidenav"
            class="btn btn-outline-primary btn-sm d-xl-none shadow-sm mx-2 my-2 px-2 py-1">
            <i class="bi bi-list fs-5"></i>
        </button>

        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
                <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Pages</a></li>
                <li class="breadcrumb-item text-sm text-dark active" aria-current="page">@yield('title')</li>
            </ol>
            <h6 class="font-weight-bolder mb-0">@yield('title')</h6>
        </nav>

    </div>
</nav>
<!-- End Navbar -->
