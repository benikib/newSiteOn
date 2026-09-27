<aside
    class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 bg-gradient-info"
    id="sidenav-main">

    <div class="sidenav-header">
        <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
            id="iconSidenav"></i>

        <a class="navbar-brand m-0 d-flex align-items-center"
            href="{{ route('dashboard') }}">

            <img src="{{ asset('assets/img/logo-ct.png') }}"
                class="navbar-brand-img h-100"
                style="border-radius:50%">

            <span class="ms-2 font-weight-bold fs-5 text-dark">
                Bisika Stock
            </span>

        </a>
    </div>


    <hr class="horizontal dark mt-0 mb-2">


    <div class="collapse navbar-collapse w-auto h-auto"
        id="sidenav-collapse-main">

        <ul class="navbar-nav">


            <!-- Dashboard -->
            <li class="nav-item">

                <a class="nav-link {{ request()->routeIs('dashboard')?'active':'' }}"
                    href="{{ route('admin.stocks.dashboard') }}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-primary text-center me-2">
                        <i class="fas fa-tachometer-alt text-white"></i>
                    </div>

                    <span class="nav-link-text">
                        Dashboard
                    </span>

                </a>

            </li>


            <!-- Gestion Stock -->
            <li class="nav-item mt-3">
                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">
                    Gestion Stock
                </h6>
            </li>



            <!-- Etablissements -->
            <li class="nav-item">

                <a class="nav-link {{request()->routeIs('etablissements.*')?'active':''}}"
                href="{{route('etablissements.index')}}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-info text-center me-2">
                        <i class="fas fa-store text-white"></i>
                    </div>

                    <span class="nav-link-text">
                        Établissements
                    </span>

                </a>

            </li>



            <!-- Catégories -->
            <li class="nav-item">

                <a class="nav-link {{request()->routeIs('categories.*')?'active':''}}"
                href="{{route('stock.categories.index')}}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-success text-center me-2">
                        <i class="fas fa-tags text-white"></i>
                    </div>

                    <span>
                        Catégories
                    </span>

                </a>

            </li>



            <!-- Unités -->
            <li class="nav-item">

                <a class="nav-link {{request()->routeIs('units.*')?'active':''}}"
                href="{{route('units.index')}}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-warning text-center me-2">
                        <i class="fas fa-balance-scale text-white"></i>
                    </div>

                    <span>
                        Unités
                    </span>

                </a>

            </li>



            <!-- Produits -->
            <li class="nav-item">

                <a class="nav-link {{request()->routeIs('products.*')?'active':''}}"
                href="{{route('stock.products.index')}}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-danger text-center me-2">
                        <i class="fas fa-box text-white"></i>
                    </div>

                    <span>
                        Produits
                    </span>

                </a>

            </li>

{{-- 


            <!-- Stock -->
            <li class="nav-item">

                <a class="nav-link {{request()->routeIs('stock.*')?'active':''}}"
                href="{{route('stock.index')}}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-dark text-center me-2">
                        <i class="fas fa-warehouse text-white"></i>
                    </div>

                    <span>
                        Gestion Stock
                    </span>

                </a>

            </li>



            <!-- Approvisionnement -->
            <li class="nav-item mt-3">

                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">
                    Approvisionnement
                </h6>

            </li>



            <!-- Fournisseurs -->
            <li class="nav-item">

                <a class="nav-link {{request()->routeIs('suppliers.*')?'active':''}}"
                href="{{route('suppliers.index')}}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-info text-center me-2">
                        <i class="fas fa-truck text-white"></i>
                    </div>

                    <span>
                        Fournisseurs
                    </span>

                </a>

            </li>



            <!-- Achats -->
            <li class="nav-item">

                <a class="nav-link {{request()->routeIs('purchases.*')?'active':''}}"
                href="{{route('purchases.index')}}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-success text-center me-2">
                        <i class="fas fa-shopping-cart text-white"></i>
                    </div>

                    <span>
                        Achats
                    </span>

                </a>

            </li>



            <!-- Rapports -->
            <li class="nav-item mt-3">

                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">
                    Analyse
                </h6>

            </li>


            <li class="nav-item">

                <a class="nav-link {{request()->routeIs('reports.*')?'active':''}}"
                href="{{route('reports.index')}}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-primary text-center me-2">
                        <i class="fas fa-chart-line text-white"></i>
                    </div>

                    <span>
                        Rapports
                    </span>

                </a>

            </li> --}}



            <!-- Compte -->
            <li class="nav-item mt-3">

                <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">
                    Mon compte
                </h6>

            </li>


            <li class="nav-item">

                <a class="nav-link"
                href="{{route('profile.edit')}}">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-dark text-center me-2">
                        <i class="fas fa-user text-white"></i>
                    </div>

                    <span>
                        Profil
                    </span>

                </a>

            </li>


            <li class="nav-item">

                <a class="nav-link"
                href="#"
                data-bs-toggle="modal"
                data-bs-target="#logoutModal">

                    <div class="icon icon-shape icon-sm shadow border-radius-md bg-gradient-secondary text-center me-2">
                        <i class="fas fa-sign-out-alt text-white"></i>
                    </div>

                    <span>
                        Déconnexion
                    </span>

                </a>

            </li>


        </ul>

    </div>

</aside>