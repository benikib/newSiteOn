@extends('layouts.base')
@section('title', 'Users')
@section('content')


    <div class="container py-4">
        <!-- Bouton Retour -->
        {{-- <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary d-inline-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M15 8a.5.5 0 0 1-.5.5H2.707l4.147 4.146a.5.5 0 0 1-.708.708l-5-5a.5.5 0 0 1 0-.708l5-5a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8z"/>
            </svg>
            Retour
        </a>
    </div> --}}

        <!-- En-tête + bouton -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 text-dark">Users</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#repportingModal">
                Ajouter un user
            </button>

        </div>

        <!-- Modal Ajout -->
        @include('admins.users.create')



        <!-- Tableau -->
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Gestion User</h6>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center justify-content-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">N°
                                        </th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Nom</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            email</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            télèphone</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder text-center opacity-7 ps-2">
                                            role</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="text-xs font-weight-bold">{{ $loop->iteration }}</td>
                                            <td class="text-xs font-weight-bold">{{ $user->name }}</td>
                                            <td class="text-xs font-weight-bold">{{ $user->email }}</td>
                                            <td class="text-xs font-weight-bold">{{ $user->telephone }}</td>
                                            <td class="text-xs font-weight-bold text-center">{{ $user->role }}</td>
                                            <td class="align-middle">
                                                <a href="#" class="text-secondary font-weight-bold text-xs"
                                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $user->id }}">
                                                    Edit
                                                </a>
                                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-danger font-weight-bold text-xs"
                                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                        Supprimer
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Modals - Placé après la table -->
                            @include('admins.users.edit')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script -->






@endsection
