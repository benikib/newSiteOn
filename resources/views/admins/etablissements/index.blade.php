@extends('layouts.base')

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
                                        <div class=" bg-opacity-10 rounded-circle p-2 me-3">
                                            <i class="fas fa-building "></i>
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
                                <!-- Colonne des statistiques -->
                                <td class="px-4 py-3">
                                    <div class="d-flex flex-wrap align-items-center gap-2">
                                        <span class="badge bg-success">
                                            {{ $etablissement->services_count ?? 0 }} services
                                        </span>
                                        <span class="badge bg-info">
                                            {{ $etablissement->promotions_count ?? 0 }} promotions
                                        </span>
                                        <span class="badge bg-warning text-dark">
                                            {{ $etablissement->publicites_count ?? 0 }} publicités
                                        </span>
                                    </div>
                                </td>

                                <!-- Colonne de la note et bouton -->
                                <td class="px-4 py-3">
                                    <div class="d-flex flex-column align-items-start gap-1">
                                        <div class="d-flex align-items-center text-warning">
                                            @php
                                                $note = round($etablissement->note_moyenne ?? 0);
                                            @endphp
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= $note)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star"></i>
                                                @endif
                                            @endfor
                                            <span
                                                class="text-muted ms-2 small">({{ number_format($etablissement->note_moyenne, 1) }})</span>
                                        </div>

                                        <button class="btn btn-sm btn-outline-primary mt-1" data-bs-toggle="modal"
                                            data-bs-target="#modalNote{{ $etablissement->id }}">
                                            Noter
                                        </button>
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-end">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('etablissement.show', $etablissement->id) }}"
                                            class="btn btn-sm btn-outline-primary" title="Voir">
                                            <i class="fas fa-edit"></i>
                                        </a>


                                        <!-- Bouton de suppression -->
                                        <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#modalDelete{{ $etablissement->id }}" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                            <!-- Modal d'ajout de note -->
                            <div class="modal fade" id="modalNote{{ $etablissement->id }}" tabindex="-1"
                                aria-labelledby="modalNoteLabel{{ $etablissement->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('etablissements.note_moyenne', $etablissement->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalNoteLabel{{ $etablissement->id }}">Ajouter
                                                    une
                                                    note</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Fermer"></button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="etablissement_id"
                                                    value="{{ $etablissement->id }}">

                                                <div class="mb-3">
                                                    <label for="note_moyenne" class="form-label">Note (0 à 5)</label>
                                                    <input type="number" step="0.1" min="0" max="5"
                                                        class="form-control" name="note_moyenne" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Enregistrer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal de confirmation -->
                            <div class="modal fade" id="modalDelete{{ $etablissement->id }}" tabindex="-1"
                                aria-labelledby="modalDeleteLabel{{ $etablissement->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalDeleteLabel{{ $etablissement->id }}">
                                                Confirmation de suppression</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Fermer"></button>
                                        </div>
                                        <div class="modal-body">
                                            Voulez-vous vraiment supprimer cet élément ?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Annuler</button>

                                            <form action="{{ route('etablissements.destroy', $etablissement->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">Supprimer</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
