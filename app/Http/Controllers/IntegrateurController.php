<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\TypeEtablissement;
use Illuminate\Http\Request;

class IntegrateurController extends Controller
{
    public function index()
    {
       $typeEtablissements = TypeEtablissement::all();
       $etablissements = Etablissement::all();
       return view ('integrateur.index', compact('typeEtablissements','etablissements'));
    }
}
