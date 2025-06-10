@extends('layouts.main')
@section('title', 'Etablissements')
@section('content')
    <div class="container-fluid py-4">
        <!-- Bouton Retour -->
        <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M15 8a.5.5 0 0 1-.5.5H2.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z" />
                </svg>
                Retour
            </a>
        </div>

        <!-- En-tête + bouton -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-dark">Etablissements</h1>
            <button type="button" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#etablissementModal">
                <i class="fas fa-plus me-2"></i>
                Ajouter un établissement
            </button>
        </div>

        <!-- Tableau -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Gestion des Etablissements</h6>
                        <div class="input-group" style="width: 300px;">
                            <input type="text" class="form-control" placeholder="Rechercher..." id="searchInput">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="etablissementsTable">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Nom
                                        </th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                            Type</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                            Adresse</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                            Téléphone</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2">
                                            Description</th>
                                        <th
                                            class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7 ps-2 text-center">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($etablissements as $etablissement)
                                        <tr class="etablissementRow{{ $etablissement->id }}">
                                            <td>
                                                <div class="d-flex px-2 align-items-center">
                                                    <div class="me-3">
                                                        <img src="{{ asset('assets/img/small-logos/logo-spotify.svg') }}"
                                                            class="avatar avatar-sm rounded-circle" alt="logo">
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-sm">{{ $etablissement->nom }}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-gradient-primary">{{ $etablissement->typeEtablissement->nom }}</span>
                                            </td>
                                            <td>
                                                <span class="text-xs font-weight-bold">{{ $etablissement->ville }},
                                                    {{ $etablissement->commune }}, {{ $etablissement->quartier }}, Av.
                                                    {{ $etablissement->avenue }} N°{{ $etablissement->numero }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="text-xs font-weight-bold">{{ $etablissement->telephone }}</span>
                                            </td>
                                            <td>
                                                <p class="text-xs text-secondary mb-0"
                                                    style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                    {{ $etablissement->description }}
                                                </p>
                                            </td>
                                            <td class="align-middle text-center">
                                                <div class="btn-group" role="group">
                                                    <!-- Bouton Détails -->
                                                    <button class="btn btn-info btn-sm position-relative"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#etablissementInfoModal{{ $etablissement->id }}"
                                                        data-bs-toggle="tooltip" title="Détails">
                                                        <i class="fas fa-info-circle"></i>
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge bg-dark d-none d-lg-block"
                                                            style="white-space: nowrap;">
                                                            Détails
                                                        </span>
                                                    </button>

                                                    <!-- Bouton Modifier -->
                                                    <button class="btn btn-warning btn-sm position-relative"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editEtablissementModal{{ $etablissement->id }}"
                                                        data-bs-toggle="tooltip" title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge bg-dark d-none d-lg-block"
                                                            style="white-space: nowrap;">
                                                            Modifier
                                                        </span>
                                                    </button>

                                                    <!-- Bouton Services -->
                                                    <button class="btn btn-primary btn-sm position-relative"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#serviceModal{{ $etablissement->id }}"
                                                        data-bs-toggle="tooltip" title="Services">
                                                        <i class="fas fa-concierge-bell"></i>
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge bg-dark d-none d-lg-block"
                                                            style="white-space: nowrap;">
                                                            Services
                                                        </span>
                                                    </button>

                                                    <!-- Bouton Publicité -->
                                                    <button class="btn btn-success btn-sm position-relative"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#publiciteModal{{ $etablissement->id }}"
                                                        data-bs-toggle="tooltip" title="Publicité">
                                                        <i class="fas fa-ad"></i>
                                                        <span
                                                            class="position-absolute top-0 start-100 translate-middle badge bg-dark d-none d-lg-block"
                                                            style="white-space: nowrap;">
                                                            Publicité
                                                        </span>
                                                    </button>

                                                    <!-- Bouton Supprimer -->
                                                    <form
                                                        action="{{ route('etablissements.destroy', $etablissement->id) }}"
                                                        method="POST" class="d-inline position-relative">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet établissement?')"
                                                            data-bs-toggle="tooltip" title="Supprimer">
                                                            <i class="fas fa-trash-alt"></i>
                                                            <span
                                                                class="position-absolute top-0 start-100 translate-middle badge bg-dark d-none d-lg-block"
                                                                style="white-space: nowrap;">
                                                                Supprimer
                                                            </span>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>

                                            <!-- Script pour activer les tooltips -->



                                        </tr>

                                        <!-- Modal Info Etablissement -->
                                        <div class="modal fade" id="etablissementInfoModal{{ $etablissement->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="etablissementInfoModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-gradient-info">
                                                        <h5 class="modal-title text-white"
                                                            id="etablissementInfoModalLabel">
                                                            Détails de l'établissement</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-4 text-center">
                                                                <img src="{{ asset('assets/img/small-logos/logo-spotify.svg') }}"
                                                                    class="img-fluid rounded-circle mb-3"
                                                                    style="width: 150px; height: 150px;" alt="logo">
                                                                <h4>{{ $etablissement->nom }}</h4>
                                                                <span
                                                                    class="badge bg-gradient-primary">{{ $etablissement->typeEtablissement->nom }}</span>
                                                            </div>
                                                            <div class="col-md-8">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <h6 class="mb-3">Informations de base</h6>
                                                                        <ul class="list-group list-group-flush">
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                                                <span><i
                                                                                        class="fas fa-phone me-2"></i>Téléphone</span>
                                                                                <span
                                                                                    class="text-dark">{{ $etablissement->telephone }}</span>
                                                                            </li>
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                                                <span><i
                                                                                        class="fas fa-map-marker-alt me-2"></i>Adresse</span>
                                                                                <span
                                                                                    class="text-dark">{{ $etablissement->ville }},
                                                                                    {{ $etablissement->commune }},
                                                                                    {{ $etablissement->quartier }}, Av.
                                                                                    {{ $etablissement->avenue }}
                                                                                    N°{{ $etablissement->numero }}</span>
                                                                            </li>
                                                                            <li
                                                                                class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                                                <span><i
                                                                                        class="fas fa-calendar-alt me-2"></i>Créé
                                                                                    le</span>
                                                                                <span
                                                                                    class="text-dark">{{ $etablissement->created_at->format('d/m/Y') }}</span>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="card mt-3">
                                                                    <div class="card-body">
                                                                        <h6 class="mb-3">Description</h6>
                                                                        <p class="text-dark">
                                                                            {{ $etablissement->description }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Fermer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Modification Etablissement -->
                                        <div class="modal fade" id="editEtablissementModal{{ $etablissement->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="editEtablissementModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-gradient-warning">
                                                        <h5 class="modal-title text-white"
                                                            id="editEtablissementModalLabel">Modifier l'établissement</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form id="editEtablissementForm{{ $etablissement->id }}"
                                                        action="{{ route('etablissements.update', $etablissement->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="edit_nom">Nom</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_nom" name="nom"
                                                                            value="{{ $etablissement->nom }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="edit_type_etablissement_id">Type
                                                                            d'établissement</label>
                                                                        <select class="form-control"
                                                                            id="edit_type_etablissement_id"
                                                                            name="type_etablissement_id" required>
                                                                            @foreach ($typeEtablissements as $type)
                                                                                <option value="{{ $type->id }}"
                                                                                    {{ $etablissement->type_etablissement_id == $type->id ? 'selected' : '' }}>
                                                                                    {{ $type->nom }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="edit_ville">Ville</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_ville" name="ville"
                                                                            value="{{ $etablissement->ville }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="edit_commune">Commune</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_commune" name="commune"
                                                                            value="{{ $etablissement->commune }}"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="edit_quartier">Quartier</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_quartier" name="quartier"
                                                                            value="{{ $etablissement->quartier }}"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="edit_avenue">Avenue</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_avenue" name="avenue"
                                                                            value="{{ $etablissement->avenue }}" required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row mt-3">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="edit_numero">Numéro</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_numero" name="numero"
                                                                            value="{{ $etablissement->numero }}" required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="edit_telephone">Téléphone</label>
                                                                        <input type="text" class="form-control"
                                                                            id="edit_telephone" name="telephone"
                                                                            value="{{ $etablissement->telephone }}"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group mt-3">
                                                                <label for="edit_description">Description</label>
                                                                <textarea class="form-control" id="edit_description" name="description" rows="3">{{ $etablissement->description }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Annuler</button>
                                                            <button type="submit" class="btn btn-warning">Enregistrer les
                                                                modifications</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Services -->
                                        <div class="modal fade" id="serviceModal{{ $etablissement->id }}" tabindex="-1"
                                            role="dialog" aria-labelledby="serviceModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-gradient-primary">
                                                        <h5 class="modal-title text-white" id="serviceModalLabel">Services
                                                            de {{ $etablissement->nom }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <h6>Liste des services</h6>
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                                data-bs-target="#addServiceModal{{ $etablissement->id }}">
                                                                <i class="fas fa-plus me-1"></i> Ajouter
                                                            </button>
                                                        </div>

                                                        @if ($etablissement->services && $etablissement->services->count() > 0)
                                                            <div class="table-responsive">
                                                                <table class="table table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nom</th>
                                                                            <th>Description</th>
                                                                            <th>Prix</th>
                                                                            <th>Actions</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @foreach ($etablissement->services as $service)
                                                                            <tr>
                                                                                <td>{{ $service->nom }}</td>
                                                                                <td>{{ Str::limit($service->description, 50) }}
                                                                                </td>
                                                                                <td>{{ number_format($service->prix, 0, ',', ' ') }}
                                                                                    FCFA</td>
                                                                                <td>
                                                                                    <button class="btn btn-sm btn-info"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#editServiceModal{{ $service->id }}">
                                                                                        <i class="fas fa-edit"></i>
                                                                                    </button>
                                                                                    <form
                                                                                        action="{{ route('services.destroy', $service->id) }}"
                                                                                        method="POST" class="d-inline">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit"
                                                                                            class="btn btn-sm btn-danger"
                                                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service?')">
                                                                                            <i class="fas fa-trash"></i>
                                                                                        </button>
                                                                                    </form>
                                                                                </td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <div class="alert alert-info">
                                                                Aucun service disponible pour cet établissement.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Fermer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal Publicité -->
                                        <div class="modal fade" id="publiciteModal{{ $etablissement->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="publiciteModalLabel"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-gradient-success">
                                                        <h5 class="modal-title text-white" id="publiciteModalLabel">
                                                            Publicités pour {{ $etablissement->nom }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div
                                                            class="d-flex justify-content-between align-items-center mb-3">
                                                            <h6>Campagnes publicitaires</h6>
                                                            <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                                                data-bs-target="#addPubliciteModal{{ $etablissement->id }}">
                                                                <i class="fas fa-plus me-1"></i> Nouvelle campagne
                                                            </button>
                                                        </div>

                                                        @if ($etablissement->publicites && $etablissement->publicites->count() > 0)
                                                            <div class="row">
                                                                @foreach ($etablissement->publicites as $publicite)
                                                                    <div class="col-md-6 mb-4">
                                                                        <div class="card h-100">
                                                                            <img src="{{ asset('storage/' . $publicite->image) }}"
                                                                                class="card-img-top" alt="Publicité"
                                                                                style="height: 180px; object-fit: cover;">
                                                                            <div class="card-body">
                                                                                <h5 class="card-title">
                                                                                    {{ $publicite->titre }}</h5>
                                                                                <p class="card-text">
                                                                                    {{ Str::limit($publicite->description, 100) }}
                                                                                </p>
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center">
                                                                                    <span
                                                                                        class="badge bg-gradient-info">{{ $publicite->statut }}</span>
                                                                                    <small class="text-muted">Du
                                                                                        {{ $publicite->date_debut->format('d/m/Y') }}
                                                                                        au
                                                                                        {{ $publicite->date_fin->format('d/m/Y') }}</small>
                                                                                </div>
                                                                            </div>
                                                                            <div class="card-footer bg-transparent">
                                                                                <div class="btn-group w-100">
                                                                                    <button
                                                                                        class="btn btn-sm btn-outline-info"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#viewPubliciteModal{{ $publicite->id }}">
                                                                                        <i class="fas fa-eye"></i> Voir
                                                                                    </button>
                                                                                    <button
                                                                                        class="btn btn-sm btn-outline-warning"
                                                                                        data-bs-toggle="modal"
                                                                                        data-bs-target="#editPubliciteModal{{ $publicite->id }}">
                                                                                        <i class="fas fa-edit"></i>
                                                                                        Modifier
                                                                                    </button>
                                                                                    <form
                                                                                        action="{{ route('publicites.destroy', $publicite->id) }}"
                                                                                        method="POST">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <button type="submit"
                                                                                            class="btn btn-sm btn-outline-danger"
                                                                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette publicité?')">
                                                                                            <i class="fas fa-trash"></i>
                                                                                            Supprimer
                                                                                        </button>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @else
                                                            <div class="alert alert-info">
                                                                Aucune campagne publicitaire pour cet établissement.
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Fermer</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">Aucun établissement
                                                trouvé.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer d-flex justify-content-between align-items-center">
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                @if (method_exists($etablissements, 'firstItem'))
                                    <div class="text-muted">
                                        Affichage de <span class="fw-bold">{{ $etablissements->firstItem() }}</span> à
                                        <span class="fw-bold">{{ $etablissements->lastItem() }}</span> sur
                                        <span class="fw-bold">{{ $etablissements->total() }}</span> entrées
                                    </div>
                                    <div>
                                        {{ $etablissements->nom }}
                                    </div>
                                @else
                                    <div class="text-muted">
                                        Total: <span class="fw-bold">{{ count($etablissements) }}</span> entrées
                                    </div>
                                @endif
                            </div>
                            <div>
                                {{ 'aucune idéé' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajout Etablissement -->
    <div class="modal fade" id="etablissementModal" tabindex="-1" role="dialog"
        aria-labelledby="etablissementModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-primary">
                    <h5 class="modal-title text-white" id="etablissementModalLabel">Ajouter un nouvel établissement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addEtablissementForm" action="{{ route('etablissements.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nom">Nom</label>
                                    <input type="text" class="form-control" id="nom" name="nom" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type_etablissement_id">Type d'établissement</label>
                                    <select class="form-control" id="type_etablissement_id" name="type_etablissement_id"
                                        required>
                                        <option value="">Sélectionnez un type</option>
                                        @foreach ($typeEtablissements as $type)
                                            <option value="{{ $type->id }}">{{ $type->nom }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ville">Ville</label>
                                    <input type="text" class="form-control" id="ville" name="ville" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="commune">Commune</label>
                                    <input type="text" class="form-control" id="commune" name="commune" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="quartier">Quartier</label>
                                    <input type="text" class="form-control" id="quartier" name="quartier" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="avenue">Avenue</label>
                                    <input type="text" class="form-control" id="avenue" name="avenue" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="numero">Numéro</label>
                                    <input type="text" class="form-control" id="numero" name="numero" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="telephone">Téléphone</label>
                                    <input type="text" class="form-control" id="telephone" name="telephone" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Recherche en temps réel
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('etablissementsTable');

            searchInput.addEventListener('keyup', function() {
                const filter = searchInput.value.toLowerCase();
                const rows = table.getElementsByTagName('tr');

                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    const cells = row.getElementsByTagName('td');
                    let found = false;

                    for (let j = 0; j < cells.length - 1; j++) { // Exclure la colonne Actions
                        const cell = cells[j];
                        if (cell) {
                            const text = cell.textContent || cell.innerText;
                            if (text.toLowerCase().indexOf(filter) > -1) {
                                found = true;
                                break;
                            }
                        }
                    }

                    row.style.display = found ? '' : 'none';
                }
            });

            // Gestion des formulaires d'édition
            document.querySelectorAll('[id^="editEtablissementForm"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const url = this.action;
                    const method = 'POST'; // Laravel utilise POST avec _method=PUT

                    fetch(url, {
                            method: method,
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Rafraîchir la page pour voir les modifications
                                location.reload();
                            } else {
                                alert('Une erreur est survenue lors de la mise à jour.');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Une erreur est survenue lors de la mise à jour.');
                        });
                });
            });

            // Initialisation des tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endsection
