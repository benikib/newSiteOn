<div class="mb-3">

<label>
Nom
</label>

<input type="text"
name="name"
class="form-control"
value="{{ old('name',$unit->name ?? '') }}"
required>

</div>



<div class="mb-3">

<label>
Symbole
</label>

<input type="text"
name="symbol"
class="form-control"
value="{{ old('symbol',$unit->symbol ?? '') }}">

</div>



<div class="mb-3">

<label>
Description
</label>


<textarea name="description"
class="form-control">{{ old('description',$unit->description ?? '') }}</textarea>


</div>



<div class="mb-3">

<label>
Statut
</label>


<select name="status"
class="form-select">


<option value="1"
{{ old('status',$unit->status ?? 1)==1?'selected':'' }}>
Actif
</option>


<option value="0"
{{ old('status',$unit->status ?? 1)==0?'selected':'' }}>
Inactif
</option>


</select>


</div>