<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use App\Models\Publicite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PubliciteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $publicites = Publicite::all();
        $etablissements = Etablissement::all();

        return view('admins.publicites.index', compact('publicites', 'etablissements'));
    }

    public function indexEtablissement(int $etablissement)
    {
        $this->authorizeManager($etablissement);
        $etablissement = Etablissement::findOrFail($etablissement);
        $publicites = Publicite::where('etablissement_id', $etablissement->id)->latest()->get();

        return view('etablissements.publicites.index', compact('etablissement', 'publicites'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(int $etablissement)
    {
        $this->authorizeManager($etablissement);
        $etablissement = Etablissement::findOrFail($etablissement);

        return view('etablissements.publicites.create', compact('etablissement'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titre' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
            'etablissement_id' => 'required|exists:etablissements,id',
            'dure' => 'required|integer|min:1',
            'image' => 'required|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov|max:51200',
            'status' => 'required|in:active,inactive,planned',
        ]);
        $this->authorizeManager((int) $validated['etablissement_id']);
        $validated['image_path'] = $request->file('image')->store('publicites', 'public');
        unset($validated['image']);

        Publicite::create($validated);

        return redirect()->back()->with('success', 'Publicité créée avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Publicite $publicite)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publicite $publicite)
    {
        $this->authorizeManager((int) $publicite->etablissement_id);

        return view('etablissements.publicites.edit', compact('publicite'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)

    {

        $publicite = Publicite::findOrFail($id);
        $validated = $request->validate([
            'titre' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date',
            'etablissement_id' => 'required|exists:etablissements,id',
            'dure' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive,planned',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,webm,mov|max:51200',
        ]);
        $this->authorizeManager((int) $publicite->etablissement_id);
        $this->authorizeManager((int) $validated['etablissement_id']);

        unset($validated['image']);
        $oldPath = $publicite->image_path;
        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('publicites', 'public');
        }
        $publicite->update($validated);
        if (isset($validated['image_path'])) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()->back()->with('success', 'Publicité mise à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publicite $publicite)
    {
        $this->authorizeManager((int) $publicite->etablissement_id);
        $imagePath = $publicite->image_path;
        $publicite->delete();
        Storage::disk('public')->delete($imagePath);

        return redirect()->back()->with('success', 'Publicité supprimée avec succès.');
    }

    private function authorizeManager(int $etablissementId): void
    {
        if (auth()->user()?->role === 'etablissement') {
            abort_unless(auth()->user()->usersEtablissements()
                ->where('etablissement_id', $etablissementId)->exists(), 404);
        }
    }
}
