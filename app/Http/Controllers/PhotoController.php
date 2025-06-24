<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Récupérer toutes les photos
        $photos = Photo::all();

        // Retourner la vue avec les photos
        return view('welcome', compact('photos'));
    }
public function storeets(Request $request)
    {
        $request->validate([
            'etablissement_id' => 'required|exists:etablissements,id',
            'photos.*' => 'required|image|max:5120', // 5MB max
            'titre' => 'nullable|string|max:255'
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('public/photos');

                Photo::create([
                    'etablissement_id' => $request->etablissement_id,
                    'image_path' => $path,
                    'titre' => $request->titre ?? 'Photo'
                ]);
            }
        }

        return back()->with('success', 'Photos ajoutées avec succès');
    }

    public function updatetitre(Request $request, Photo $photo)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255'
        ]);

        $photo->update($validated);

        return back()->with('success', 'Photo mise à jour');
    }

    public function destroyphoto(Photo $photo)
    {
        // Supprimer le fichier physique ici si nécessaire
        $photo->delete();

        return back()->with('success', 'Photo supprimée');
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

   public function stores(Request $request)
    {
        $request->validate([
            'etablissement_id' => 'required|exists:etablissements,id',
            'photos.*' => 'required|image|max:5120', // 5MB max
            'titre' => 'nullable|string|max:255'
        ]);

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('photos', 'public');

                Photo::create([
                    'etablissement_id' => $request->etablissement_id,
                    'image_path' => $path,
                    'titre' => $request->titre ?? 'Photo'
                ]);
            }
        }

        return back()->with('success', 'Photos ajoutées avec succès');
    }

     public function store(Request $request)
{

   try {

        $validated = $request->validate([
        'titre' => 'required|string|max:255',

    ]);


  //  // Méthode CORRECTE pour WAMP/Windows :
    $path = $request->file('image')->store('photos', 'public');

    // Enregistrez le chemin RELATIF sans 'public/'
    $validated['image_path'] = $path; // 'photos/filename.jpg'

    // Solution 2 - Si vous préférez garder l'ancienne structure
    // $path = $request->file('image')->store('photos'); // Sans 'public/'
    // $validated['image_path'] = $path;






    // Création de la photo

    $photo = Photo::create($validated);


    return redirect()->back()->with('success', 'Photo ajoutée avec succès');


    } catch (\Exception $e) {

        return back()->withErrors(['error' => 'Erreur lors de l\'ajout de la photo : ' . $e->getMessage()]);
    }
}
public function destroye( $gallery)
{
    Storage::delete('public/' . $gallery->image_path);
    $gallery->delete();
    return back()->with('success', 'Photo supprimée');
}

    /**
     * Display the specified resource.
     */
    public function show(Photo $photo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */


    public function update(Request $request, Photo $photo)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:255'
        ]);

        $photo->update($validated);

        return back()->with('success', 'Photo mise à jour');
    }

    public function destroy(Photo $photo)
    {
        // Supprimer le fichier physique ici si nécessaire
        $photo->delete();

        return back()->with('success', 'Photo supprimée');
    }
}
