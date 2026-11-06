@extends('layouts.public')

@section('title', 'Gestion des publications BISIKA')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h2 mb-0">Gestion des publications</h1>
        <a class="btn btn-primary" href="{{ route('publications.create') }}">Nouvelle publication</a>
    </div>
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Titre</th><th>Auteur</th><th>Catégorie</th><th>Publication</th><th>Action</th></tr></thead>
                <tbody>
                    @forelse ($publications as $publication)
                        <tr>
                            <td>{{ $publication->titre }}</td>
                            <td>{{ $publication->organisationPatronale?->nom ?? $publication->auteur?->name ?? 'BISIKA' }}</td>
                            <td>{{ $publication->categorie }}</td>
                            <td>{{ $publication->publie_le?->format('d/m/Y H:i') ?? 'Non programmée' }}</td>
                            <td>
                                <form method="POST" action="{{ route('publications.destroy', $publication) }}"
                                    onsubmit="return confirm('Supprimer cette publication ?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Aucune publication.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-body">{{ $publications->links() }}</div>
    </div>
</div>
@endsection
