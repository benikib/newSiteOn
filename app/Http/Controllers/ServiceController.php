<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Service;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($etablissement)
    {

        $services = Service::where('etablissement_id', $etablissement)->get();
        $etablissement = Etablissement::findOrFail($etablissement);
        if (!$etablissement) {
            return redirect()->route('etablissements.index')->with('error', 'Établissement non trouvé.');
        }

        return view('admins.services.index', compact('services','etablissement'));
    }

      public function indexEtablissement($etablissement)
    {
          if (auth()->user()?->role === 'etablissement') {
              abort_unless(auth()->user()->usersEtablissements()
                  ->where('etablissement_id', $etablissement)->exists(), 404);
          }
          $services = Service::where('etablissement_id', $etablissement)->get();
        $etablissement = Etablissement::findOrFail($etablissement);
        if (!$etablissement) {
            return redirect()->route('etablissements.index')->with('error', 'Établissement non trouvé.');
        }

        return view('etablissements.services.index', compact('services','etablissement'));
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
                'description' => 'nullable|string|max:255',
                'commentaire' => 'nullable|string|max:2000',
                'etablissement_id' => 'required|exists:etablissements,id',
                'prix' => 'nullable|numeric|min:0',
                'icone' => 'nullable|in:calendar,truck,phone,star',
                'disponibilite' => 'nullable|boolean',
            ]);

                if ($request->user()->role === 'etablissement') {
                    abort_unless($request->user()->usersEtablissements()
                        ->where('etablissement_id', $request->input('etablissement_id'))->exists(), 404);
                }

                Service::create([
                'nom' => $request->input('nom'),
                'description' => $request->input('description'),
                'commentaire' => $request->input('commentaire'),
                'etablissement_id' => $request->input('etablissement_id'),
                'prix' => $request->input('prix', null), // Default to null if not provided
                'icone' => $request->input('icone', 'calendar'),
                'disponibilite' => $request->boolean('disponibilite', true),
            ]);

            return redirect()->back()
                             ->with('success', 'Service créé avec succès.');
            //return redirect()->route('services.index', ['etablissement' => $request->input('etablissement_id')])
        // ->with('success', 'Service créé avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        try {
            $service = Service::findOrFail($id);
            if ($request->user()->role === 'etablissement') {
                abort_unless($request->user()->usersEtablissements()
                    ->where('etablissement_id', $service->etablissement_id)->exists(), 404);
            }

            $validated = $request->validate([
                'nom' => 'required|string|max:100',
                'description' => 'nullable|string|max:255',
                'commentaire' => 'nullable|string|max:2000',
                'prix' => 'nullable|numeric|min:0',
                'icone' => 'sometimes|in:calendar,truck,phone,star',
                'disponibilite' => 'sometimes|boolean',
            ]);

            $service->update($validated);

            return  back()->with('success', 'Service ajouté avec succès');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }
    public function promotion(Request $request, Service $service){
       
        try {
            $request->validate([
                'promotion' => 'required|numeric|min:0|max:100',
                'date_debut_promo' => 'required|date',
                'date_fin_promo' => 'required|date|after_or_equal:date_debut_promo',
            ]);

           $serviceok = $service->update([
                'promotion' => $request->input('promotion'),
                'date_debut_promo' => $request->input('date_debut_promo'),
                'date_fin_promo' => $request->input('date_fin_promo'),
            ]);
       

            return redirect()->back()->with('success', 'Promotion mise à jour avec succès.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        try {
            if (auth()->user()?->role === 'etablissement') {
                abort_unless(auth()->user()->usersEtablissements()
                    ->where('etablissement_id', $service->etablissement_id)->exists(), 404);
            }
            $service->delete();
            return redirect()->back()->with('success', 'Service supprimé avec succès.');
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->withErrors(['error' => 'Impossible de supprimer ce service. Réessayez.']);
        }
    }

    public function gallery(Service $service)
    {
            if (auth()->user()?->role === 'etablissement') {
                abort_unless(auth()->user()->usersEtablissements()
                    ->where('etablissement_id', $service->etablissement_id)->exists(), 404);
            }

            return view('services.gallery', ['service' => $service->load('photos')]);
    }

    public function storePhoto(Request $request, Service $service)
    {
            if ($request->user()->role === 'etablissement') {
                abort_unless($request->user()->usersEtablissements()
                    ->where('etablissement_id', $service->etablissement_id)->exists(), 404);
            }
            $data = $request->validate([
                'titre' => 'nullable|string|max:255',
                'photo' => 'required|image|max:5120',
            ]);
            $path = $request->file('photo')->store('photos/services', 'public');
            $service->photos()->create([
                'titre' => $data['titre'] ?? $service->nom,
                'image_path' => $path,
                'etablissement_id' => $service->etablissement_id,
                'est_publique' => true,
            ]);

            return back()->with('success', 'Photo ajoutée à la galerie du service.');
    }

    public function destroyPhoto(Service $service, Photo $photo)
    {
            if (auth()->user()?->role === 'etablissement') {
                abort_unless(auth()->user()->usersEtablissements()
                    ->where('etablissement_id', $service->etablissement_id)->exists(), 404);
            }
            abort_unless($photo->service_id === $service->id, 404);
            Storage::disk('public')->delete($photo->image_path);
            $photo->delete();

            return back()->with('success', 'Photo supprimée de la galerie.');
    }
}
