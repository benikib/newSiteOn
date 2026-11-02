@extends('layouts.main')

@section('title', 'Créer une publicité')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-4">Créer une publicité · {{ $etablissement->nom }}</h1>
    <form class="card card-body" method="POST" enctype="multipart/form-data" action="{{ route('etablissements.publicites.store', $etablissement->id) }}">
        @csrf
        <input type="hidden" name="etablissement_id" value="{{ $etablissement->id }}">
        <div class="mb-3"><label class="form-label">Titre</label><input class="form-control" name="titre" required maxlength="100"></div>
        <div class="mb-3"><label class="form-label">Synopsis</label><textarea class="form-control" name="description" rows="3" maxlength="255"></textarea></div>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Date de début</label><input class="form-control" type="date" name="date" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Durée (jours)</label><input class="form-control" type="number" min="1" name="dure" required></div>
        </div>
        <div class="mb-3"><label class="form-label">Fichier média</label><input class="form-control" type="file" name="image" accept="image/*,video/mp4,video/webm,video/quicktime" required><small class="text-muted">Image ou vidéo MP4/WebM/MOV, maximum 50 Mo.</small></div>
        <input type="hidden" name="status" value="active">
        <div class="d-flex gap-2"><button class="btn btn-primary">Créer</button><a class="btn btn-outline-secondary" href="{{ route('etablissements.publicites.index', $etablissement->id) }}">Annuler</a></div>
    </form>
</div>
@endsection
