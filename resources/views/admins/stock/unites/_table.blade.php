<div class="card">

<div class="card-body">

<table class="table table-bordered">

<thead>

<tr>
    <th>#</th>
    <th>Nom</th>
    <th>Symbole</th>
    <th>Description</th>
    <th>Statut</th>
    <th>Actions</th>
</tr>

</thead>


<tbody>

@forelse($units as $unit)

<tr>

<td>
{{ $loop->iteration }}
</td>


<td>
{{ $unit->name }}
</td>


<td>
{{ $unit->symbol }}
</td>


<td>
{{ $unit->description }}
</td>


<td>

@if($unit->status)

<span class="badge bg-success">
Actif
</span>

@else

<span class="badge bg-danger">
Inactif
</span>

@endif

</td>


<td>

<a href="{{ route('units.edit',$unit->id) }}"
class="btn btn-sm btn-warning">

<i class="fas fa-edit"></i>

</a>


<form action="{{ route('units.destroy',$unit->id) }}"
method="POST"
class="d-inline">

@csrf
@method('DELETE')

<button class="btn btn-sm btn-danger"
onclick="return confirm('Supprimer cette unité ?')">

<i class="fas fa-trash"></i>

</button>

</form>


</td>

</tr>


@empty

<tr>

<td colspan="6" class="text-center">

Aucune unité trouvée

</td>

</tr>

@endforelse


</tbody>

</table>


</div>

</div>