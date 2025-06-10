<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\TypeEtablissement;
use App\Models\UserEtablissement;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth ;

class UserEtablissementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
  public function index()
{
    $typeEtablissements = TypeEtablissement::all();

    $etablissements = Etablissement::whereHas('users', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->with(['photos', 'services'])
        ->paginate(5);

    return view('etablissements.index', compact('etablissements', 'typeEtablissements'));
}
public function promotion()
{
    $user = Auth::user();
   $etablissements = Etablissement::whereHas('users', function($query) use ($user) {
        $query->where('user_id', $user->id);
    })->with(['services']);
    

    return view('etablissements.promotions.index', compact('etablissements'));
}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(UserEtablissement $userEtablissement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserEtablissement $userEtablissement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, UserEtablissement $userEtablissement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserEtablissement $userEtablissement)
    {
        //
    }
}
