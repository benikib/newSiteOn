@extends('layouts.main')
@section('title', 'Promotion')
@section('content')


    <div class="container py-4">


        <!-- En-tête + bouton -->
        {{-- <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-dark">Etablissement {{ $etablissement->nom }}</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#repportingModal">
                Ajouter un Promotion
            </button>

        </div> --}}

        <!-- Modal Ajout -->
        {{-- @include('admins.services.create') --}}



        <!-- Tableau -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Gestion des service de l'etablissement</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center justify-content-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">N°
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Nom</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Description</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Prix</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Actions</th>
                                    </tr>
                                <tbody>
                                    {{ dd($etablissements) }}
                                    @foreach ($etablissements->services as $service)
                                        <tr>
                                            <td class="text-xs font-weight-bold">{{ $loop->iteration }}</td>
                                            <td class="text-xs font-weight-bold">{{ $service->nom }}</td>
                                            <td class="text-xs font-weight-bold">{{ $service->description }}</td>
                                            <td class="text-xs font-weight-bold">{{ $service->prix }}</td>
                                            <td class="align-middle">
                                                <div class="d-flex">
                                                    <button class="btn btn-link text-secondary mb-0" data-bs-toggle="modal"
                                                        data-bs-target="#editModal{{ $service->id }}">
                                                        <i class="fas fa-edit text-secondary"></i>
                                                    </button>
                                                    {{-- <form action="{{ route('services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce service ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger mb-0">
                            <i class="fas fa-trash text-danger"></i>
                        </button>
                    </form> --}}
                                                </div>
                                    @endforeach

                                </tbody>
                            </table>

                            <!-- Modals - Placé après la table -->
                            {{-- @include('admins.services.edit') --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->






@endsection
