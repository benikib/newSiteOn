@extends('layouts.stockadmin')

@section('title','Modifier unité')


@section('content')

<div class="container py-4">


<div class="card">


<div class="card-header">
Modifier : {{ $unit->name }}
</div>


<div class="card-body">


<form action="{{ route('units.update',$unit->id) }}"
method="POST">


@csrf
@method('PUT')


@include('admins.units._form')


<button class="btn btn-success">
Mettre à jour
</button>


</form>


</div>


</div>


</div>

@endsection