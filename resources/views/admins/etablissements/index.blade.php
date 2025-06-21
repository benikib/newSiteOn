@extends('layouts.base')
@section('title', 'Etablissement')
@section('content')


    <div class="container py-4">
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
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#repportingModal">
                Ajouter un Etablissement
            </button>

        </div>

        <!-- Modal Ajout -->
        @include('admins.etablissements.create')



        <!-- Tableau -->
        <!-- Tableau des Établissements -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">Liste des Établissements</h5>
                    <div class="d-flex gap-2">
                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0" id="searchInput"
                                placeholder="Rechercher un établissement...">
                        </div>
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-filter me-1"></i>Filtrer
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="dataTable">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4 py-3 fw-semibold">Nom</th>
                                <th class="border-0 px-4 py-3 fw-semibold">Type</th>
                                <th class="border-0 px-4 py-3 fw-semibold">Ville</th>
                                <th class="border-0 px-4 py-3 fw-semibold">Statistiques</th>
                                <th class="border-0 px-4 py-3 fw-semibold">Note</th>
                                <th class="border-0 px-4 py-3 fw-semibold text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($etablissements ?? [] as $etablissement)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $etablissement->nom }}</div>
                                                {{-- <div class="text-muted small">{{ $etablissement->description }}</div> --}}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="badge bg-light text-dark">{{ $etablissement->typeEtablissement->nom ?? 'N/A' }}</span>
                                    </td>
                                    <td class="px-4 py-3">{{ $etablissement->ville ?? 'N/A' }}</td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-1">
                                            <span class="badge bg-success">{{ $etablissement->services_count ?? 0 }}
                                                services</span>
                                            <span class="badge bg-info">{{ $etablissement->promotions_count ?? 0 }}
                                                promotions</span>
                                            <span class="badge bg-warning">{{ $etablissement->publicites_count ?? 0 }}
                                                publicités</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="text-warning me-2">
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star"></i>
                                                <i class="fas fa-star-o"></i>
                                            </div>
                                            <span class="text-muted small">(4.0)</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-building fa-3x mb-3"></i>
                                            <h5>Aucun établissement trouvé</h5>
                                            <p>Commencez par ajouter votre premier établissement</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#addEtablissementModal">
                                                <i class="fas fa-plus me-2"></i>Ajouter un établissement
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('#dataTable tbody tr');

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();

                tableRows.forEach(function(row) {
                    const rowText = row.textContent.toLowerCase();
                    row.style.display = rowText.includes(query) ? '' : 'none';
                });
            });
        });
    </script>
    <!-- Script -->






@endsection
