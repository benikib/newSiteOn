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
    <!-- Bouton pour ouvrir le modal -->
    @php
        $taux = \App\Models\TauxDeChange::where('date', today())->first();
    @endphp

    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTauxModal">
        Taux du jour : {{ $taux ? number_format($taux->usd_cdf, 2) : 'Non défini' }} CDF
    </button>



</nav>
<!-- Modal d’édition du taux -->
<div class="modal fade" id="editTauxModal" tabindex="-1" aria-labelledby="editTauxModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTauxModalLabel">Modifier le taux du {{ now()->format('d/m/Y') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('taux.update', $taux?->id ?? 0) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="usd_cdf" class="form-label">Taux USD → CDF</label>
                        <input type="number" step="0.01" class="form-control" name="usd_cdf"
                            value="{{ $taux?->usd_cdf ?? '' }}" required>
                    </div>

                    <input type="hidden" name="date" value="{{ date('Y-m-d') }}">

                    <button type="submit" class="btn btn-success">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>


<!-- End Navbar -->
