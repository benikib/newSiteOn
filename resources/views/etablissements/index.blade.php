@extends('layouts.main')
@section('title', 'Etablissements')
@section('content')
    <div class="container-fluid py-4">


        <!-- En-tête + bouton -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-dark">Etablissements</h1>
            {{-- <button type="button" class="btn btn-primary d-inline-flex align-items-center" data-bs-toggle="modal"
                data-bs-target="#etablissementModal">
                <i class="fas fa-plus me-2"></i>
                Ajouter un établissement
            </button> --}}
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
                                        <th>Nom</th>
                                        <th>Type</th>

                                        <th>Téléphone</th>

                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($etablissements as $etablissement)
                                        @if ($etablissement->statut !== 'actif')
                                            <tr>
                                                <td colspan="6" class="text-center text-warning py-4">
                                                    {{ $etablissement->nom }} - <a href="">Passer au
                                                        paiement</a>
                                                </td>
                                            </tr>
                                        @else
                                            @include('etablissements.partials.row', [
                                                'etablissement' => $etablissement,
                                            ])
                                        @endif
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">
                                                Aucun établissement trouvé.
                                            </td>
                                        </tr>
                                    @endforelse

                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if (method_exists($etablissements, 'currentPage'))
                            <!-- Cas paginé -->
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Affichage de <span class="fw-bold">{{ $etablissements->firstItem() }}</span> à
                                    <span class="fw-bold">{{ $etablissements->lastItem() }}</span> sur
                                    <span class="fw-bold">{{ $etablissements->total() }}</span> entrées
                                </div>
                                <div>
                                    {{ $etablissements->links() }}
                                </div>
                            </div>
                        @else
                            <!-- Cas non paginé -->
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Total: <span class="fw-bold">{{ $etablissements->count() }}</span> entrées
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajout Etablissement -->
    @include('etablissements.modals.create')




    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Recherche en temps réel
            const searchInput = document.getElementById('searchInput');
            const table = document.getElementById('etablissementsTable');

            if (searchInput && table) {
                searchInput.addEventListener('keyup', function() {
                    const filter = searchInput.value.toLowerCase();
                    const rows = table.querySelectorAll('tbody tr');

                    rows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        let found = false;

                        for (let j = 0; j < cells.length - 1; j++) {
                            const cell = cells[j];
                            if (cell) {
                                const text = cell.textContent || cell.innerText;
                                if (text.toLowerCase().includes(filter)) {
                                    found = true;
                                    break;
                                }
                            }
                        }

                        row.style.display = found ? '' : 'none';
                    });
                });
            }

            // Initialisation des tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(tooltipTriggerEl => {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    <style>
        /* Si la couleur pink n'est pas définie */
        .btn-pink {
            background-color: #e83e8c;
            color: white;
            border-color: #e83e8c;
        }

        .btn-pink:hover {
            background-color: #d4357c;
            border-color: #d4357c;
        }

        /* Pour un espacement uniforme entre les boutons */
        .btn-group .btn {
            margin-right: 0.25rem;
        }

        .btn-group .btn:last-child {
            margin-right: 0;
        }

        .bg-gradient-pink {
            background: linear-gradient(135deg, #ff6b9d 0%, #ff8fab 100%);
        }
    </style>
@endsection
