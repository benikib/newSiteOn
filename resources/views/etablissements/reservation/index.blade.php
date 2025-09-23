@extends('layouts.main')
@section('title', 'Gestion des Réservations')
@section('content')

    <div class="container py-4">
        <!-- En-tête avec bouton d'ajout -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-dark">Gestion des Réservations</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addReservationModal">
                <i class="fas fa-plus me-1"></i> Nouvelle Réservation
            </button>
        </div>

        <!-- Modal Ajout Réservation -->
        <div class="modal fade" id="addReservationModal" tabindex="-1" aria-labelledby="addReservationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addReservationModalLabel">Nouvelle Réservation</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('reservations.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_name" class="form-label">Nom du client <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="client_name" name="client_name"
                                            required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="client_phone" class="form-label">Téléphone</label>
                                        <input type="tel" class="form-control" id="client_phone" name="client_phone">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="service_id" class="form-label">Service <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="service_id" name="service_id" required>
                                            <option value="">Sélectionnez un service</option>
                                            @foreach ($services as $service)
                                                <option value="{{ $service->id }}">{{ $service->nom }} -
                                                    {{ $service->prix }} $</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="date" class="form-label">Date et heure <span
                                                class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="date" name="date"
                                            required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes supplémentaires</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
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

        <!-- Modal Ajout Paiement -->
        <div class="modal fade" id="paiementModal" tabindex="-1" aria-labelledby="paiementModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="paiementModalLabel">Enregistrer un Paiement</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form id="formPaiement" method="POST" action="{{ route('paiements.store') }}">
                        @csrf
                        <input type="hidden" name="reservation_id" id="reservation_id">
                        <input type="hidden" name="service_id" id="modal_service_id">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Client</label>
                                <input type="text" class="form-control" name="client" id="modal_client_name"
                                    readonly>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Service</label>
                                <input type="text" class="form-control" id="modal_service_name" readonly>
                            </div>

                            <div class="mb-3">
                                <label for="montant" class="form-label">Montant <span
                                        class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="montant" name="montant" required>
                            </div>

                            <div class="mb-3">
                                <label for="date_paiement" class="form-label">Date du paiement <span
                                        class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="date_paiement" name="date_paiement"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-success">Enregistrer Paiement</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tableau des Réservations -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Liste des Réservations</h5>
                <div class="input-group" style="width: 300px;">
                    <input type="text" class="form-control" placeholder="Rechercher..." id="searchInput">
                    <button class="btn btn-outline-secondary" type="button">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="reservationsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Client</th>
                                <th>Téléphone</th>
                                <th>Service</th>
                                <th>Date/Heure</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservations as $reservation)
                                <tr>
                                    <td>{{ $reservation->client_name }}</td>
                                    <td>{{ $reservation->client_phone ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $reservation->service->nom }}</span>
                                        <br>
                                        <small>{{ $reservation->service->prix }} $</small>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($reservation->date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $reservation->statut === 'confirmé' ? 'success' : ($reservation->statut === 'rejeté' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($reservation->statut) }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        @if ($reservation->statut === 'en_attente')
                                            <button class="btn btn-sm btn-success me-1"
                                                onclick="changerStatut({{ $reservation->id }}, 'confirmé')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                onclick="changerStatut({{ $reservation->id }}, 'rejeté')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @elseif($reservation->statut === 'confirmé')
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#paiementModal"
                                                data-reservation-id="{{ $reservation->id }}"
                                                data-client-name="{{ $reservation->client_name }}"
                                                data-service-id="{{ $reservation->service->id }}"
                                                data-service-name="{{ $reservation->service->nom }}"
                                                data-service-prix="{{ $reservation->service->prix }}">
                                                <i class="fas fa-money-bill-wave"></i> Payer
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Aucune réservation trouvée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($reservations->hasPages())
                    <div class="card-footer">
                        {{ $reservations->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Gestion du modal de paiement
        document.getElementById('paiementModal').addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const modal = this;

            // Récupération des données
            modal.querySelector('#reservation_id').value = button.getAttribute('data-reservation-id');
            modal.querySelector('#modal_client_name').value = button.getAttribute('data-client-name');
            modal.querySelector('#modal_service_id').value = button.getAttribute('data-service-id');
            modal.querySelector('#modal_service_name').value = button.getAttribute('data-service-name');
            modal.querySelector('#montant').value = button.getAttribute('data-service-prix');
        });

        // Fonction pour changer le statut
        function changerStatut(id, statut) {
            if (!confirm(`Voulez-vous vraiment marquer cette réservation comme "${statut}" ?`)) return;

            fetch(`/reservations/${id}/statut`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        statut
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Erreur: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue');
                });
        }

        // Recherche dans le tableau
        document.getElementById('searchInput').addEventListener('input', function() {
            const value = this.value.toLowerCase();
            document.querySelectorAll('#reservationsTable tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(value) ? '' : 'none';
            });
        });
    </script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const form = document.querySelector('#addReservationModal form');

    form.addEventListener("submit", async (e) => {
        e.preventDefault(); // 👉 empêche le navigateur d'aller sur l'URL

        const url = form.action;   // garde le route('reservations.store')
        const formData = new FormData(form);

        try {
            const response = await fetch(url, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: formData
            });

            if (response.ok) {
                const data = await response.json();
                alert(data.message || "Réservation enregistrée avec succès !");

                // Fermer la modale Bootstrap
                const modal = bootstrap.Modal.getInstance(document.getElementById('addReservationModal'));
                modal.hide();

                // Réinitialiser le formulaire
                form.reset();
            } else if (response.status === 422) {
                const errors = await response.json();
                let messages = Object.values(errors.errors).flat().join("\n");
                alert("Erreur de validation :\n" + messages);
            } else {
                alert("Erreur " + response.status);
            }
        } catch (error) {
            console.error(error);
            alert("Impossible de contacter le serveur.");
        }
    });
});
</script>

@endsection
