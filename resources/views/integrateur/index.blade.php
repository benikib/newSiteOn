@extends('layouts.base')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Aside -->
        <aside class="col-md-3 col-lg-2 bg-light p-3 vh-100 d-flex flex-column gap-4 shadow-sm">
            <h2 class="h5 mb-4">Menu</h2>
            <nav class="nav flex-column gap-2">
                <a class="nav-link active" href="#etablissements"><i class="fas fa-building me-2"></i> Etablissements</a>
                <a class="nav-link" href="#publicites"><i class="fas fa-bullhorn me-2"></i> Publicité</a>
                <a class="nav-link" href="#profil"><i class="fas fa-user me-2"></i> Profil</a>
                <a class="nav-link text-danger" href="#deconnexion"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a>
            </nav>
        </aside>
        <!-- Main content -->
        <main class="col-md-9 col-lg-10 py-4">
            <h1>Espace Intégrateur</h1>
            <div id="etablissements" class="d-flex flex-column align-items-start gap-3 mt-4">
                <!-- En-tête + bouton -->
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 gap-3">
                    <h1 class="h4 text-dark mb-0">Etablissements</h1>
                    <button type="button" class="btn btn-primary w-100 w-md-auto" data-bs-toggle="modal"
                        data-bs-target="#repportingModal">
                        <i class="fas fa-plus d-md-none me-2"></i>
                        <span class="d-none d-md-inline">Ajouter un Etablissement</span>
                        <span class="d-md-none">Ajouter</span>
                    </button>
                </div>
                <!-- Modal Ajout -->
                @include('admins.etablissements.create')
            </div>
            <div id="publicites" class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h4 text-dark">Publicité</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#repportingModal">
                    Ajouter une publicité
                </button>
            </div>
            <!-- Modal Ajout -->
            @include('admins.publicites.create')
            <div id="profil" class="mt-5">
                <h2 class="h5">Profil</h2>
                <!-- Ajoute ici les infos du profil de l'intégrateur -->
            </div>
            <div id="deconnexion" class="mt-4">
                <a href="{{ route('logout') }}" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt me-2"></i> Déconnexion</a>
            </div>
        </main>
    </div>
</div>
@endsection
