@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Gestion du Personnel</h1>
            <a href="{{ route('personnels.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter un membre
            </a>
        </div>

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
                                    <td>{{ $personnel->email }}</td>
                                    <td>{{ $personnel->telephone ?? '-' }}</td>
                                    <td>{{ $personnel->poste ?? '-' }}</td>
                                    <td>
                                        @foreach ($personnel->equipes as $equipe)
                                            <span class="badge bg-primary">{{ $equipe->nom }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <a href="{{ route('personnels.edit', $personnel->id) }}"
                                            class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('personnels.destroy', $personnel->id) }}" method="POST"
                                            class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Êtes-vous sûr ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">Aucun personnel enregistré</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $personnels->links() }}
            </div>
        </div>
    </div>
@endsection
