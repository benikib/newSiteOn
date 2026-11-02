@extends('layouts.public')

@section('title', 'Galerie du service')

@section('content')
<div class="container py-4">
    <h1 class="h3">Galerie : {{ $service->nom }}</h1>
    @if (Route::is('services.photos'))
        @php($storeRoute = 'services.photos.store')
        @php($deleteRoute = 'services.photos.destroy')
    @else
        @php($storeRoute = 'etablissements.services.photos.store')
        @php($deleteRoute = 'etablissements.services.photos.destroy')
    @endif
    <form method="POST" enctype="multipart/form-data" action="{{ route($storeRoute, $service) }}" class="card card-body mb-4">
        @csrf
        <div class="row g-3 align-items-end">
            <div class="col-md-4"><label class="form-label">Titre (facultatif)</label><input name="titre" class="form-control" maxlength="255"></div>
            <div class="col-md-5"><label class="form-label">Photo</label><input type="file" name="photo" class="form-control" accept="image/*" required></div>
            <div class="col-md-3"><button class="btn btn-primary">Ajouter à la galerie</button></div>
        </div>
    </form>
    <div class="row g-3">
        @forelse ($service->photos as $photo)
            <div class="col-sm-6 col-lg-3">
                <div class="card h-100">
                    <img src="{{ asset('storage/'.ltrim(str_replace('public/', '', $photo->image_path), '/')) }}" class="card-img-top" alt="{{ $photo->titre }}" style="height: 190px; object-fit: cover;">
                    <div class="card-body"><h2 class="h6">{{ $photo->titre }}</h2>
                        <form method="POST" action="{{ route($deleteRoute, [$service, $photo]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Aucune photo dans la galerie de ce service.</p>
        @endforelse
    </div>
</div>
@endsection
