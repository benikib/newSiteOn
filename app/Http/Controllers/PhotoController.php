<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Publicite;
use App\Models\TypeEtablissement;
use App\Models\Product;
use App\Models\ProductReservationItem;
use App\Models\DeliveryOrderItem;
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
        $photos = $publicitesActives = Publicite::actives()
        ->orderBy('id', 'desc')
        ->get();

            $photos = Publicite::actives()
                ->orderByDesc('id')
                ->get();

            return view('welcome.simple', compact('photos'));
    }

    public function products(Request $request)
    {
        [$products] = $this->publicProducts();
        $query = trim((string) $request->query('q', ''));

        if ($query !== '') {
            $normalizedQuery = $this->normalizeSearch($query);
            $products = $products->filter(function ($product) use ($normalizedQuery) {
                $searchable = $this->normalizeSearch(implode(' ', [
                    $product->name,
                    $product->description,
                    $product->code,
                    $product->category->nom ?? '',
                    $product->etablissement->nom ?? '',
                ]));

                return str_contains($searchable, $normalizedQuery);
            })->values();
        }

        $categories = $products->pluck('category')
            ->filter(fn ($category) => $category && $category->status)
            ->unique('id')
            ->values();

        return view('products.index', compact('products', 'categories', 'query'));
    }

    private function publicProducts(): array
    {
        $products = Product::with(['stock', 'unit', 'etablissement', 'category'])
            ->where('status', true)
            ->whereHas('etablissement', fn ($query) => $query->where('statut', 'actif'))
            ->orderByDesc('created_at')
            ->get();

        $productIds = $products->pluck('id');
        $reservedQuantities = ProductReservationItem::whereIn('product_id', $productIds)
            ->whereHas('reservation', fn ($query) => $query->where('statut', 'en_attente'))
            ->selectRaw('product_id, SUM(quantity) as total_reserved')
            ->groupBy('product_id')
            ->pluck('total_reserved', 'product_id');

        $deliveryQuantities = DeliveryOrderItem::whereIn('product_id', $productIds)
            ->whereHas('deliveryOrder', fn ($query) => $query->whereIn('statut', ['en_attente', 'payee_partiellement', 'payee']))
            ->selectRaw('product_id, SUM(quantity) as total_reserved')
            ->groupBy('product_id')
            ->pluck('total_reserved', 'product_id');

        foreach ($products as $product) {
            $physicalQuantity = (int) ($product->stock->quantity ?? 0);
            $pendingQuantity = (int) ($reservedQuantities[$product->id] ?? 0)
                + (int) ($deliveryQuantities[$product->id] ?? 0);
            $product->setAttribute('available_quantity', max(0, $physicalQuantity - $pendingQuantity));
        }

        $establishments = $products->pluck('etablissement')->filter()->unique('id')->values();

        return [$products, $establishments];
    }

    private function normalizeSearch(string $value): string
    {
        $normalized = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return mb_strtolower($normalized === false ? $value : $normalized, 'UTF-8');
    }

public function storess(Request $request)
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
        report($e);
        return back()->withErrors(['error' => 'Impossible d’ajouter cette photo. Vérifiez le fichier et réessayez.']);
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
