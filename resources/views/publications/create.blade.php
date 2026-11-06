@extends('layouts.public')

@section('title', 'Créer une publication BISIKA')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h2 mb-0">Nouvelle publication</h1>
        <a class="btn btn-outline-secondary" href="{{ route('publications.manage') }}">Gérer les publications</a>
    </div>

    <form method="POST" action="{{ route('publications.store') }}" class="card card-body shadow-sm row g-3">
        @csrf
        <div class="col-md-8">
            <label class="form-label" for="titre">Titre</label>
            <input class="form-control" id="titre" name="titre" value="{{ old('titre') }}" maxlength="200" required>
            @error('titre')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label" for="categorie">Catégorie</label>
            <select class="form-select" id="categorie" name="categorie" required>
                @foreach (['Vulgarisation', 'Rappel', 'News'] as $category)
                    <option value="{{ $category }}" @selected(old('categorie', 'News') === $category)>{{ $category }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="cible">Audience</label>
            <select class="form-select" id="cible" name="cible" required>
                <option value="tous" @selected(old('cible', 'tous') === 'tous')>Tous les acteurs</option>
                <option value="domaines" @selected(old('cible') === 'domaines')>Domaines d’activité sélectionnés</option>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="synopsis">Synopsis</label>
            <input class="form-control" id="synopsis" name="synopsis" value="{{ old('synopsis') }}" maxlength="500">
        </div>
        <div class="col-12">
            <label class="form-label" for="contenu">Contenu</label>
            <textarea class="form-control" id="contenu" name="contenu" rows="5" maxlength="20000" required>{{ old('contenu') }}</textarea>
            @error('contenu')<div class="text-danger small">{{ $message }}</div>@enderror
        </div>
        <fieldset class="col-12">
            <legend class="form-label">Domaines ciblés</legend>
            <div class="d-flex flex-wrap gap-3">
                @foreach ($typesEtablissement as $type)
                    <label class="form-check">
                        <input class="form-check-input" type="checkbox" name="types_etablissement[]" value="{{ $type->id }}"
                            @checked(in_array($type->id, old('types_etablissement', [])))>
                        <span class="form-check-label">{{ $type->nom }}</span>
                    </label>
                @endforeach
            </div>
            @error('types_etablissement')<div class="text-danger small">{{ $message }}</div>@enderror
        </fieldset>
        <div class="col-md-6">
            <label class="form-label" for="ville">Ville ciblée (facultatif)</label>
            <input class="form-control" id="ville" name="ville" value="{{ old('ville') }}" maxlength="255">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="commune">Commune ciblée (facultatif)</label>
            <input class="form-control" id="commune" name="commune" value="{{ old('commune') }}" maxlength="255">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="publie_le">Date de publication (facultative)</label>
            <input class="form-control" type="datetime-local" id="publie_le" name="publie_le" value="{{ old('publie_le') }}">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="expire_le">Date d’expiration (facultative)</label>
            <input class="form-control" type="datetime-local" id="expire_le" name="expire_le" value="{{ old('expire_le') }}">
        </div>
        <div class="col-12 d-flex gap-2">
            <button class="btn btn-primary" type="submit">Publier</button>
            <a class="btn btn-outline-secondary" href="{{ route('publications.index') }}">Annuler</a>
        </div>
    </form>
</div>
@endsection
