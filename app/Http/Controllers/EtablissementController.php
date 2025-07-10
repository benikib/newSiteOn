<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Photo;
use App\Models\Promotion;
use App\Models\TypeEtablissement;
use App\Models\User;
use App\Models\UserEtablissement;
use App\Models\Service;
use App\Models\Publicite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EtablissementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $etablissements =  $etablissements = Etablissement::withCount(['services', 'promotions', 'publicites', 'photos'])
            ->with(['typeEtablissement', 'services', 'promotions'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        $typeEtablissements = TypeEtablissement::all();


        return view('admins.etablissements.index', compact('etablissements', 'typeEtablissements'));
    }
    public function users_ets()
    {
       
       $userEtablissements = UserEtablissement::with('user') 
            ->where('etablissement_id', Auth::user()->usersEtablissements->first()?->etablissement_id)    
            ->get();
         

            return view('etablissements.users_etablissements.index', compact('userEtablissements'));
        
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
        try {


            $request->validate([
                'nom' => 'required|string|max:100',
                'ville' => 'required|string|max:100',
                'commune' => 'required|string|max:100',
                'avenue' => 'nullable|string|max:100',
                'quartier' => 'nullable|string|max:100',
                'numero' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'telephone' => 'nullable|string|max:20',
                'type_etablissement_id' => 'required|exists:type_etablissements,id',
                'user_name' => 'nuLlable|string|max:100',
                'user_email' => 'nullable|email|max:100|unique:users,email',
                'user_password' => 'nullable|string|min:8',
                'user_role' => 'nullable',
            ]);


            if (Auth::user()->role === 'admin') {

                $user = User::create([
                    'name' => $request->user_name,
                    'email' => $request->user_email,
                    'password' => Hash::make($request->user_password),
                    'role' => $request->user_role,
                ]);
                $etablissements = Etablissement::create($request->all());
                UserEtablissement::create([
                    'user_id' => $user->id,
                    'etablissement_id' => $etablissements->id,
                ]);
            } elseif (Auth::user()->role === 'etablissement') {

                $etablissements = Etablissement::create($request->all());
                UserEtablissement::create([
                    'user_id' => Auth::user()->id,
                    'etablissement_id' => $etablissements->id,
                ]);

            } else {

                return redirect()->back()->withErrors(['error' => 'Vous n\'êtes pas autorisé à créer un établissement.']);
            }
            return redirect()->back()->with('success', 'Établissement créé avec succès.');


        } catch (\Illuminate\Validation\ValidationException $e) {
            dd($e->validator->errors());

            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( $etablissement)
    {
           $etablissement = \App\Models\Etablissement::with('typeEtablissement')->findOrFail($etablissement);
            $typesEtablissement =TypeEtablissement::all();
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Etablissement not found.');
        }

    return view('admins.etablissements.show', compact('etablissement', 'typesEtablissement'));
    }
     public function showOne( $etablissement)
    {
           $etablissement = \App\Models\Etablissement::with('typeEtablissement')->findOrFail($etablissement);
            $typesEtablissement =TypeEtablissement::all();
        if (!$etablissement) {
            return redirect()->back()->with('error', 'Etablissement not found.');
        }

    return view('etablissements.partials.show', compact('etablissement', 'typesEtablissement'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Etablissement $etablissement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $etablissement = Etablissement::findOrFail($id);

            $request->validate([
                'nom' => 'required|string|max:100',
                'ville' => 'required|string|max:100',
                'commune' => 'required|string|max:100',
                'avenue' => 'nullable|string|max:100',
                'quartier' => 'nullable|string|max:100',
                'numero' => 'nullable|string|max:50',
                'description' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'telephone' => 'nullable|string|max:20',
                'type_etablissement_id' => 'required|exists:type_etablissements,id',

            ]);

            $etablissemen = $etablissement->update($request->all());


            return redirect()->back()->with('success', 'Établissement mis à jour avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
public function note_moyenne(Request $request, Etablissement $etablissement)
    {
        
        $request->validate([
            'note_moyenne' => 'required|numeric|min:1|max:5',
        ]);

        try {
            $etablissement->update([
                
                'note_moyenne' => $request->note_moyenne,
            ]);

            return redirect()->back()->with('success', 'Note ajoutée avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de l\'ajout de la note.']);
        }
    }
     public function destroy(Etablissement $etablissement)
    {
        try {
            $etablissement->delete();
            return redirect()->back()->with('success', 'Établissement supprimé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression de l\'établissement.']);
        }
    }
    // EtablissementController.php
    public function dashboard()
    {
        $stats = [
            'total_etablissements' => Etablissement::count(),
            'promotions_actives' => Promotion::where('date_fin', '>=', now())->count(),
            'total_services' => Service::count(),
            'total_publicites' => Publicite::count(),
            'etablissements_recents' => Etablissement::where('created_at', '>=', now()->subDays(30))->count(),
            'services_recents' => Service::where('created_at', '>=', now()->subDays(30))->count(),
            'promotions_recents' => Promotion::where('created_at', '>=', now()->subDays(30))->count(),
            'publicites_recents' => Publicite::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        $typeEtablissements = TypeEtablissement::all();
        $etablissements = Etablissement::withCount(['services', 'promotions', 'publicites', 'photos'])
            ->with(['typeEtablissement', 'services', 'promotions'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Statistiques par type d'établissement
        $statsParType = TypeEtablissement::withCount('etablissements')->get();

        return view('etablissements.dashboard', compact('stats', 'etablissements', 'typeEtablissements', 'statsParType'));
    }
    public function updatetitre(Request $request, Etablissement $etablissement)
    {

        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'type_etablissement_id' => 'required|exists:type_etablissements,id'
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Profil mis à jour avec succès');
    }

    public function updatePhoto(Request $request, Etablissement $etablissement)
    {
        $request->validate([
            'photo' => 'required|image|max:2048'
        ]);

        // Supprimer l'ancienne photo si elle existe
        if ($etablissement->photos->isNotEmpty()) {
            // Ici vous devriez implémenter la suppression du fichier physique
            $etablissement->photos()->delete();
        }

        // Enregistrer la nouvelle photo
        $path = $request->file('photo')->store('photos', 'public');

        $etablissement->photos()->create([
            'image_path' => $path,
            'titre' => 'Photo de profil'
        ]);


        return back()->with('success', 'Photo de profil mise à jour');
    }

    public function updateContact(Request $request, Etablissement $etablissement)
    {

        $validated = $request->validate([
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255'
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Contacts mis à jour');
    }

    public function updateDescription(Request $request, Etablissement $etablissement)
    {
        $validated = $request->validate([
            'description' => 'nullable|string'
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Description mise à jour');
    }

    public function updateAddress(Request $request, Etablissement $etablissement)
    {
        $validated = $request->validate([
            'ville' => 'required|string|max:255',
            'commune' => 'required|string|max:255',
            'avenue' => 'required|string|max:255',
            'numero' => 'required|string|max:20'
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Adresse mise à jour');
    }

    public function updateSocial(Request $request, Etablissement $etablissement)
    {
        $validated = $request->validate([
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255'
        ]);

        $etablissement->update($validated);

        return back()->with('success', 'Réseaux sociaux mis à jour');
    }
}

