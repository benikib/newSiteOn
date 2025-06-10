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
        $validated = $request->validate([
        'titre' => 'required|string|max:255',
        'description' => 'nullable|string',
        'image' => 'required|image|max:2048',
        'etablissement_id' => 'required|exists:etablissements,id',
        'service_id' => 'nullable|exists:services,id',
        'promotion_id' => 'nullable|exists:promotions,id',
        'status' => 'required|in:active,inactive,pending'
    ]);

  //  // Méthode CORRECTE pour WAMP/Windows :
    $path = $request->file('image')->store('photos', 'public');

    // Enregistrez le chemin RELATIF sans 'public/'
    $validated['image_path'] = $path; // 'photos/filename.jpg'

    // Solution 2 - Si vous préférez garder l'ancienne structure
    // $path = $request->file('image')->store('photos'); // Sans 'public/'
    // $validated['image_path'] = $path;

    $validated['url'] = Str::uuid()->toString();
    // Génération d'une URL unique
    $validated['url'] = Str::uuid()->toString();


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
    public function edit(Photo $photo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Photo $photo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photo $photo)
    {
        //
    }
}
