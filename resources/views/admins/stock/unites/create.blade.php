@extends('layouts.stockadmin')

@section('title','Ajouter une unité')


@section('content')

<div class="container py-4">


<div class="card">

<div class="card-header">
Nouvelle unité
</div>


<div class="card-body">


<form action="{{ route('units.store') }}"
method="POST">

@csrf


@include('admins.stock.unites._form')


<button class="btn btn-primary">
Enregistrer
</button>


</form>


</div>

</div>


</div>

@endsection