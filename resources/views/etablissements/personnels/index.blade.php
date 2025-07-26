@extends('layouts.main')
@section('title', 'Personnel - Gestion des Établissements')
@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion du Personnel</h1>
            <!-- Bouton pour ouvrir le modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPersonnelModal">
                <i class="fas fa-plus me-1"></i> Ajouter un membre
            </button>
        </div>

        <!-- Modal d'ajout -->
        <div class="modal fade" id="addPersonnelModal" tabindex="-1" aria-labelledby="addPersonnelModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="addPersonnelModalLabel">Nouveau membre</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('personnels.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <input type="hidden" name="etablissement_id"
                                    value="{{ auth()->user()->etablissement_id }}">
                                <label for="nom" class="form-label">Nom *</label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom"
                                    name="nom" value="{{ old('nom') }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="poste" class="form-label">Poste</label>
                                <input type="text" class="form-control @error('poste') is-invalid @enderror"
                                    id="poste" name="poste" value="{{ old('poste') }}">
                                @error('poste')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                    id="telephone" name="telephone" value="{{ old('telephone') }}">
                                @error('telephone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <input type="hidden" name="etablissement_id" value="{{ auth()->user()->etablissement_id }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                            <button type="submit" class="btn btn-primary">Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tableau du personnel -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Poste</th>
                                <th>Équipes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($personnels as $personnel)
                                <tr>
                                    <td>{{ $personnel->nom }}</td>
                                    <td>{{ $personnel->email ?? '-' }}</td>
                                    <td>{{ $personnel->telephone ?? '-' }}</td>
                                    <td>{{ $personnel->poste ?? '-' }}</td>
                                    <td>
                                        @forelse($personnel->equipes as $equipe)
                                            <span class="badge bg-primary">{{ $equipe->nom }}</span>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex">
                                            <a href="{{ route('personnels.edit', $personnel->id) }}"
                                                class="btn btn-sm btn-warning me-1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('personnels.destroy', $personnel->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Êtes-vous sûr ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">Aucun personnel enregistré</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($personnels->hasPages())
                    <div class="card-footer d-flex justify-content-center">
                        {{ $personnels->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
