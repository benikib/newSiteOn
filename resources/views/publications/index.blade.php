@extends('layouts.public')

@section('title', 'Actualités des organisations patronales')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h1 class="h2 mb-0">Actualités et informations</h1>
        @auth
            @if (auth()->user()->role === 'admin' || auth()->user()->role === 'organisation_patronale' || auth()->user()->can_publish)
                <a class="btn btn-primary" href="{{ route('publications.create') }}">Créer une publication</a>
            @endif
        @endauth
    </div>
    <form method="GET" action="{{ route('publications.index') }}" class="row g-2 mb-4">
        <div class="col-md-6">
            <label class="form-label" for="type_id">Filtrer par domaine d’activité</label>
            <select class="form-select" id="type_id" name="type_id">
                <option value="">Tous les domaines</option>
                @foreach ($typesEtablissement as $type)
                    <option value="{{ $type->id }}" @selected($typeId === $type->id)>{{ $type->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="categorie">Catégorie</label>
            <select class="form-select" id="categorie" name="categorie">
                <option value="">Toutes les catégories</option>
                @foreach (['Vulgarisation', 'Rappel', 'News'] as $option)
                    <option value="{{ $option }}" @selected($category === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="ville">Ville</label>
            <input class="form-control" id="ville" name="ville" value="{{ request('ville') }}" maxlength="255">
        </div>
        <div class="col-md-3">
            <label class="form-label" for="commune">Commune</label>
            <input class="form-control" id="commune" name="commune" value="{{ request('commune') }}" maxlength="255">
        </div>
        <div class="col-auto align-self-end"><button class="btn btn-primary">Filtrer</button></div>
    </form>
    <div class="row g-3">
        @forelse ($publications as $publication)
            <div class="col-12">
                <article class="card shadow-sm">
                    <div class="card-body">
                        <div class="small text-muted">
                            {{ $publication->organisationPatronale?->nom ?? $publication->auteur?->name ?? 'BISIKA' }}
                            · {{ $publication->categorie }}
                            · {{ $publication->publie_le?->format('d/m/Y H:i') }}
                        </div>
                        <h2 class="h4 mt-2">{{ $publication->titre }}</h2>
                        @if ($publication->synopsis)<p class="lead fs-6">{{ $publication->synopsis }}</p>@endif
                        <p class="mb-2">{!! nl2br(e($publication->contenu)) !!}</p>
                        @if ($publication->cible === 'domaines')
                            <div class="small text-muted">Domaines : {{ $publication->typesEtablissement->pluck('nom')->join(', ') }}</div>
                        @endif
                        @if ($publication->ville)
                            <div class="small text-muted">Zone : {{ $publication->commune ? $publication->commune.', ' : '' }}{{ $publication->ville }}</div>
                        @endif
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">Aucune information disponible pour ce filtre.</p></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $publications->links() }}</div>
</div>
@endsection
