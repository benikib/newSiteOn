@extends('layouts.main')

@section('title', 'Modifier une publicité')

@section('content')
<div class="container py-4">
    <h1 class="h4 mb-4">Modifier la publicité</h1>
    <form class="card card-body" method="POST" enctype="multipart/form-data" action="{{ route('etablissements.publicites.update', $publicite->id) }}">
        @csrf @method('PUT')
        <input type="hidden" name="etablissement_id" value="{{ $publicite->etablissement_id }}">
        <div class="mb-3"><label class="form-label">Titre</label><input class="form-control" name="titre" value="{{ $publicite->titre }}" required maxlength="100"></div>
        <div class="mb-3"><label class="form-label">Synopsis</label><textarea class="form-control" name="description" rows="3" maxlength="255">{{ $publicite->description }}</textarea></div>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Date de début</label><input class="form-control" type="date" name="date" value="{{ \Illuminate\Support\Carbon::parse($publicite->date)->format('Y-m-d') }}" required></div>
            <div class="col-md-6 mb-3"><label class="form-label">Durée (jours)</label><input class="form-control" type="number" min="1" name="dure" value="{{ $publicite->dure }}" required></div>
        </div>
        <div class="mb-3"><label class="form-label">Remplacer le média (facultatif)</label><input class="form-control" type="file" name="image" accept="image/*,video/mp4,video/webm,video/quicktime"></div>
        <input type="hidden" name="status" value="{{ $publicite->status }}">
        <div class="d-flex gap-2"><button class="btn btn-primary">Enregistrer</button><a class="btn btn-outline-secondary" href="{{ route('etablissements.publicites.index', $publicite->etablissement_id) }}">Retour</a></div>
    </form>
</div>
@endsection
