<?php

namespace App\Http\Controllers;

use App\Models\Publication;
use App\Models\TypeEtablissement;
use Illuminate\Http\Request;

class PublicationController extends Controller
{
    public function index(Request $request)
    {
        $typesEtablissement = TypeEtablissement::orderBy('nom')->get();
        $typeId = $request->integer('type_id') ?: null;
        abort_if($typeId && !$typesEtablissement->contains('id', $typeId), 404);

        $publications = Publication::enLigne()
            ->with(['organisationPatronale', 'typesEtablissement'])
            ->where(function ($query) use ($typeId) {
                $query->where('cible', 'tous');
                if ($typeId) {
                    $query->orWhereHas('typesEtablissement', fn ($types) => $types->whereKey($typeId));
                }
            })
            ->latest('publie_le')
            ->paginate(12)
            ->withQueryString();

        return view('publications.index', compact('publications', 'typesEtablissement', 'typeId'));
    }
}
