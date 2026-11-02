@extends('layouts.main')

@section('title', 'Publicités de '.$etablissement->nom)

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <h1 class="h4">Publicités · {{ $etablissement->nom }}</h1>
        <a class="btn btn-primary" href="{{ route('etablissements.publicites.create', $etablissement->id) }}">Créer une publicité</a>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Titre</th><th>Synopsis</th><th>Période</th><th>Statut</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse ($publicites as $publicite)
                    <tr>
                        <td>{{ $publicite->titre }}</td>
                        <td>{{ $publicite->description }}</td>
                        <td>{{ $publicite->date }} · {{ $publicite->dure }} jour(s)</td>
                        <td>{{ $publicite->status }}</td>
                        <td class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('etablissements.publicites.edit', $publicite) }}">Modifier</a>
                            <form method="POST" action="{{ route('etablissements.publicites.destroy', $publicite->id) }}" onsubmit="return confirm('Supprimer cette publicité ?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">Aucune publicité pour cet établissement.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
