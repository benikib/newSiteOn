@extends('layouts.base')
@section('title', 'Etablissement')
@section('content')

    <div class="container py-4">
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

        <!-- Barre de recherche -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control border-start-0" id="searchInput"
                        placeholder="Rechercher par nom, ville ou type...">
                </div>
            </div>
        </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="dataTable">
                        <thead>
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

                <!-- Message aucun résultat -->
                <div id="noResultsMessage" class="text-center text-muted py-4 d-none">
                    <i class="fas fa-search fa-2x mb-2"></i>
                    <p>Aucun établissement ne correspond à votre recherche.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Script JS pour recherche -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('#dataTable tbody tr');
            const noResultsMessage = document.getElementById('noResultsMessage');

            // Fonction pour normaliser les accents
            function normalize(str) {
                return str
                    ?.normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .toLowerCase() || '';
            }

            searchInput.addEventListener('input', function() {
                const search = normalize(this.value);
                let visibleCount = 0;

                tableRows.forEach(function(row) {
                    const text = normalize(row.textContent);
                    const match = text.includes(search);
                    row.style.display = match ? '' : 'none';
                    if (match) visibleCount++;
                });

                // Afficher ou masquer le message "Aucun résultat"
                noResultsMessage.classList.toggle('d-none', visibleCount > 0);
            });
        });
    </script>

@endsection
