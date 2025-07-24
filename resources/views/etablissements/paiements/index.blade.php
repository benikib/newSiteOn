@extends('layouts.main')
@section('title', 'Paiements')
@section('content')

    <div class="container py-4">
        <!-- En-tête + bouton -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-dark">Paiements</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#paiementModal">
                Ajouter un Paiement
            </button>
        </div>

        <!-- Modal Paiement -->
        <div class="modal fade" id="paiementModal" tabindex="-1" aria-labelledby="paiementModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="paiementModalLabel">Nouveau Paiement</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="formPaiement" method="POST" action="{{ route('etablissements.paiements.store') }}">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="client_name" class="form-label">Nom du client</label>
                                <input type="text" class="form-control" id="client_name" name="client_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="client_phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="client_phone" name="client_phone">
                            </div>
                            <div class="mb-3">
                                <label for="service_id" class="form-label">Service</label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">Sélectionnez un service</option>
                                    @forelse ($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                    @empty
                                    @endforelse

                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant</label>
                                <input type="number" class="form-control" id="montant" name="montant" required>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="mb-3">
                                <label for="methode" class="form-label">Méthode de paiement</label>
                                <select class="form-select" id="methode" name="methode" required>
                                    <option value="espèces">Espèces</option>
                                    <option value="carte">Carte bancaire</option>
                                    <option value="mobile">Mobile money</option>
                                    <option value="virement">Virement</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tableau Paiements -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Paiements enregistrés</h6>
                        <div class="input-group" style="width: 300px;">
                            <input type="text" class="form-control" placeholder="Rechercher..." id="searchPaiements">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="paiementsTable">
                                <thead>
                                    <tr>
                                        <th>Nom client</th>
                                        <th>Téléphone</th>
                                        <th>Service</th>
                                        <th>Montant</th>
                                        <th>Méthode</th>
                                        <th>Date</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($paiements as $paiement)
                                        <tr>
                                            <td>{{ $paiement->client_name }}</td>
                                            <td>{{ $paiement->client_phone ?? '-' }}</td>
                                            <td>{{ $paiement->service->nom ?? '-' }}</td>
                                            <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                            <td>{{ ucfirst($paiement->methode) }}</td>
                                            <td>{{ date('d/m/Y', strtotime($paiement->date)) }}</td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-info me-1" data-bs-toggle="modal"
                                                    data-bs-target="#editPaiementModal"
                                                    onclick="editPaiement({{ $paiement->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger"
                                                    onclick="deletePaiement({{ $paiement->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Aucun paiement trouvé.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if (method_exists($paiements, 'currentPage'))
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Affichage de <strong>{{ $paiements->firstItem() }}</strong> à
                                    <strong>{{ $paiements->lastItem() }}</strong> sur
                                    <strong>{{ $paiements->total() }}</strong> paiements
                                </div>
                                <div>
                                    {{ $paiements->links() }}
                                </div>
                            </div>
                        @else
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Total : <strong>{{ $paiements->count() }}</strong> paiements
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        function editPaiement(id) {
            // Récupérer les données du paiement via AJAX et remplir le formulaire d'édition
            fetch(`/paiements/${id}/edit`)
                .then(response => response.json())
                .then(data => {
                    // Remplir le formulaire d'édition avec les données
                    document.getElementById('edit_client_name').value = data.client_name;
                    document.getElementById('edit_client_phone').value = data.client_phone;
                    document.getElementById('edit_service_id').value = data.service_id;
                    document.getElementById('edit_montant').value = data.montant;
                    document.getElementById('edit_methode').value = data.methode;
                    document.getElementById('edit_date').value = data.date;
                    document.getElementById('edit_form').action = `/paiements/${id}`;

                    // Afficher le modal d'édition
                    new bootstrap.Modal(document.getElementById('editPaiementModal')).show();
                })
                .catch(error => console.error('Error:', error));
        }

        function deletePaiement(id) {
            if (confirm("Voulez-vous vraiment supprimer ce paiement ?")) {
                fetch(`/paiements/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        alert(data.message);
                        location.reload();
                    })
                    .catch(error => console.error('Error:', error));
            }
        }

        // Recherche dans le tableau
        document.getElementById('searchPaiements').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#paiementsTable tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchValue) ? '' : 'none';
            });
        });
    </script>

    <!-- Modal d'édition (ajoutez ceci après le modal de création) -->
    <div class="modal fade" id="editPaiementModal" tabindex="-1" aria-labelledby="editPaiementModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPaiementModalLabel">Modifier Paiement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="edit_form" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_client_name" class="form-label">Nom du client</label>
                            <input type="text" class="form-control" id="edit_client_name" name="client_name"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_client_phone" class="form-label">Téléphone</label>
                            <input type="text" class="form-control" id="edit_client_phone" name="client_phone">
                        </div>
                        <div class="mb-3">
                            <label for="edit_service_id" class="form-label">Service</label>
                            <select class="form-select" id="edit_service_id" name="service_id" required>
                                <option value="">Sélectionnez un service</option>
                                @foreach ($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_montant" class="form-label">Montant</label>
                            <input type="number" class="form-control" id="edit_montant" name="montant" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="edit_date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_methode" class="form-label">Méthode de paiement</label>
                            <select class="form-select" id="edit_methode" name="methode" required>
                                <option value="espèces">Espèces</option>
                                <option value="carte">Carte bancaire</option>
                                <option value="mobile">Mobile money</option>
                                <option value="virement">Virement</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
