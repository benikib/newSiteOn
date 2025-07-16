@extends('layouts.main')
@section('title', 'Users des Etablissements')
@section('content')


    <div class="container py-4">
        <!-- Bouton Retour -->
        {{-- <div class="mb-3">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M15 8a.5.5 0 0 1-.5.5H2.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z" />
                </svg>
                Retour
            </a>
        </div> --}}

        <!-- En-tête + bouton -->
        {{-- <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-dark">Users</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#repportingModal">
                Ajouter un user
            </button>

        </div> --}}

        <!-- Modal Ajout -->
        {{-- @include('admins.users_etablissements.create') --}}



        <!-- Tableau -->
        <!-- Tableau Réservations -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <h6>Réservations enregistrées</h6>
                        <div class="input-group" style="width: 300px;">
                            <input type="text" class="form-control" placeholder="Rechercher..." id="searchReservations">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0" id="reservationsTable">
                                <thead>
                                    <tr>
                                        <th>Nom client</th>
                                        <th>Téléphone</th>
                                        <th>Service</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($reservations as $reservation)
                                        <tr>
                                            <td>{{ $reservation->client_name }}</td>
                                            <td>{{ $reservation->client_phone ?? '-' }}</td>
                                            <td>{{ $reservation->service->nom ?? '-' }}</td>
                                            <td>{{ $reservation->date }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $reservation->statut === 'confirmé' ? 'success' : ($reservation->statut === 'rejeté' ? 'danger' : 'secondary') }}">
                                                    {{ ucfirst($reservation->statut) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if ($reservation->statut === 'en_attente')
                                                    <button class="btn btn-sm btn-success me-1"
                                                        onclick="changerStatut({{ $reservation->id }}, 'confirmé')">Confirmer</button>
                                                    @if ($reservation->statut === 'en_attente' || $reservation->statut === 'confirmé')
                                                        <button class="btn btn-sm btn-danger"
                                                            onclick="changerStatut({{ $reservation->id }}, 'rejeté')">Annuler</button>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Aucune action</span>
                                                @endif
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Aucune réservation
                                                trouvée.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if (method_exists($reservations, 'currentPage'))
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Affichage de <strong>{{ $reservations->firstItem() }}</strong> à
                                    <strong>{{ $reservations->lastItem() }}</strong> sur
                                    <strong>{{ $reservations->total() }}</strong> réservations
                                </div>
                                <div>
                                    {{ $reservations->links() }}
                                </div>
                            </div>
                        @else
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Total : <strong>{{ $reservations->count() }}</strong> réservations
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- Script pour changer le statut -->
    <script>
        function changerStatut(id, statut) {
            if (!confirm("Confirmer cette action ?")) return;

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
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    location.reload(); // recharge pour voir les changements
                })
                .catch(() => alert("Erreur lors de la mise à jour."));
        }
    </script>

    <!-- Script -->






@endsection
