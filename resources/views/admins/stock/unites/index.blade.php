@extends('layouts.stockadmin')

@section('title','Unités')

@section('content')

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h4>Gestion des unités</h4>

        <a href="{{ route('units.create') }}" 
           class="btn btn-primary">
            Ajouter une unité
        </a>

    </div>


    @include('admins.stock.unites._table')


</div>

@endsection