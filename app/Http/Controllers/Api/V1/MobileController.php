<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Client\SaleController;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\DeliveryOrder;
use App\Models\Etablissement;
use App\Models\Inventory;
use App\Models\Movement;
use App\Models\Photo;
use App\Models\Product;
use App\Models\ProductReservation;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\Stock;
use App\Models\TypeEtablissement;
use App\Models\Unit;
use App\Services\PurchasePrice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class MobileController extends Controller
{
    public function establishmentTypes(): JsonResponse
    {
        return response()->json([
            'data' => TypeEtablissement::query()
                ->orderBy('nom')
                ->get(['id', 'nom', 'description'])
                ->map(fn (TypeEtablissement $type) => [
                    'id' => $type->id,
                    'name' => $type->nom,
                    'description' => $type->description,
                ]),
        ]);
    }

    public function establishments(Request $request): JsonResponse
    {
        $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'type_id' => ['sometimes', 'integer', 'exists:type_etablissements,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);
        $query = Etablissement::query()
            ->where('statut', 'actif')
            ->with('typeEtablissement:id,nom');

        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->where(function ($builder) use ($term) {
                $builder->where('nom', 'like', "%{$term}%")
                    ->orWhere('ville', 'like', "%{$term}%")
                    ->orWhereHas('services', fn ($services) => $services->where('nom', 'like', "%{$term}%"));
            });
        }

        if ($request->filled('type_id')) {
            $query->where('type_etablissement_id', $request->integer('type_id'));
        }

        $page = $query->orderBy('nom')->paginate(max(1, min($request->integer('per_page', 20), 50)));

        return $this->paginated($page, fn (Etablissement $item) => $this->establishmentData($item));
    }

    public function establishment(Etablissement $etablissement): JsonResponse
    {
        abort_unless($etablissement->statut === 'actif', 404);

        $etablissement->load([
            'typeEtablissement:id,nom',
            'services',
            'photos' => fn ($query) => $query->where('status', 'actif')->where('est_publique', true),
            'promotions' => fn ($query) => $query
                ->where('est_active', true)
                ->where('date_debut', '<=', now())
                ->where('date_fin', '>=', now())
                ->where('nombre_utilisations_restantes', '>', 0),
            'publicites' => fn ($query) => $query->actives(),
        ]);

        return response()->json(['data' => $this->establishmentData($etablissement, true)]);
    }

    public function publicEstablishmentPhotos(Etablissement $etablissement): JsonResponse
    {
        abort_unless($etablissement->statut === 'actif', 404);

        return response()->json([
            'data' => $etablissement->photos()
                ->where('status', 'actif')
                ->where('est_publique', true)
                ->orderBy('ordre')
                ->orderBy('id')
                ->get()
                ->map(fn ($photo) => $this->establishmentPhotoData($photo)),
        ]);
    }

    public function establishmentPhotos(int $etablissement): JsonResponse
    {
        return response()->json([
            'data' => Etablissement::findOrFail($etablissement)->photos()
                ->orderBy('ordre')
                ->orderBy('id')
                ->get()
                ->map(fn (Photo $photo) => $this->establishmentPhotoData($photo)),
        ]);
    }

    public function publicServices(Etablissement $etablissement): JsonResponse
    {
        abort_unless($etablissement->statut === 'actif', 404);

        $services = $etablissement->services()
            ->where('disponibilite', true)
            ->orderBy('nom')
            ->paginate(20);

        return $this->paginated($services, fn (Service $service) => [
            'id' => $service->id,
            'name' => $service->nom,
            'description' => $service->description,
            'price' => $service->prix,
            'duration' => $service->duree,
            'category' => $service->categorie,
            'promotion' => $service->promotion,
            'promotion_starts_at' => $service->date_debut_promo,
            'promotion_ends_at' => $service->date_fin_promo,
        ]);
    }

    public function publicProducts(Request $request, Etablissement $etablissement): JsonResponse
    {
        $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ]);
        abort_unless($etablissement->statut === 'actif', 404);

        $query = Product::query()
            ->with(['stock', 'category:id,nom', 'unit:id,name,symbol'])
            ->withSum([
                'reservationItems as pending_reservation_quantity' => fn ($items) => $items
                    ->whereHas('reservation', fn ($reservation) => $reservation->where('statut', 'en_attente')),
            ], 'quantity')
            ->withSum([
                'deliveryItems as pending_delivery_quantity' => fn ($items) => $items
                    ->whereHas('deliveryOrder', fn ($order) => $order->whereIn('statut', [
                        'en_attente', 'payee_partiellement', 'payee',
                    ])),
            ], 'quantity')
            ->where('etablissement_id', $etablissement->id)
            ->where('status', true);

        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->where(function ($builder) use ($term) {
                $builder->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        $page = $query->orderBy('name')->paginate(max(1, min($request->integer('per_page', 20), 50)));

        return $this->paginated($page, function (Product $product) {
            $data = $this->productData($product);
            if ($data['stock']) {
                $data['stock']['available_quantity'] = max(
                    0,
                    $product->stock->quantity
                        - (int) $product->pending_reservation_quantity
                        - (int) $product->pending_delivery_quantity
                );
                unset($data['stock']['quantity']);
            } else {
                $data['stock'] = [
                    'available_quantity' => 0,
                    'selling_price' => null,
                    'selling_price_original' => null,
                    'selling_currency' => null,
                ];
            }

            return $data;
        });
    }

    public function createProductReservation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);
        $items = collect($validated['items'])->keyBy('product_id');

        $request->merge([
            'product_ids' => $items->keys()->values()->all(),
            'quantities' => $items->map(fn ($item) => $item['quantity'])->all(),
        ]);

        return app(\App\Http\Controllers\ReservationController::class)->storeArticles($request);
    }

    public function createDeliveryOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);
        $items = collect($validated['items'])->keyBy('product_id');

        $request->merge([
            'product_ids' => $items->keys()->values()->all(),
            'quantities' => $items->map(fn ($item) => $item['quantity'])->all(),
        ]);

        return app(\App\Http\Controllers\ReservationController::class)->storeDelivery($request);
    }

    public function establishmentsForUser(Request $request): JsonResponse
    {
        $items = Etablissement::query()
            ->where('statut', 'actif')
            ->whereHas('users', fn ($query) => $query->where('users.id', $request->user()->id))
            ->orderBy('nom')
            ->get();

        return response()->json([
            'data' => $items->map(fn (Etablissement $item) => $this->establishmentData($item)),
        ]);
    }

    public function updateCoordinates(Request $request, int $etablissement): JsonResponse
    {
        $shortKeys = $request->exists('lat') || $request->exists('lng');
        $longKeys = $request->exists('latitude') || $request->exists('longitude');

        if ($shortKeys && $longKeys) {
            throw ValidationException::withMessages([
                'coordinates' => 'Envoyez soit lat/lng, soit latitude/longitude.',
            ]);
        }

        $fields = $shortKeys
            ? [
                'lat' => ['required', 'numeric', 'between:-90,90'],
                'lng' => ['required', 'numeric', 'between:-180,180'],
            ]
            : [
                'latitude' => ['required', 'numeric', 'between:-90,90'],
                'longitude' => ['required', 'numeric', 'between:-180,180'],
            ];
        $coordinates = $request->validate($fields);
        $etablissement = Etablissement::findOrFail($etablissement);

        $etablissement->update([
            'latitude' => $coordinates['latitude'] ?? $coordinates['lat'],
            'longitude' => $coordinates['longitude'] ?? $coordinates['lng'],
        ]);

        return response()->json([
            'data' => [
                'id' => $etablissement->id,
                'latitude' => $etablissement->latitude === null ? null : (float) $etablissement->latitude,
                'longitude' => $etablissement->longitude === null ? null : (float) $etablissement->longitude,
            ],
        ]);
    }

    public function uploadEstablishmentPhoto(Request $request, int $etablissement): JsonResponse
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'max:5120'],
        ]);
        $etablissement = Etablissement::findOrFail($etablissement);
        $oldPath = $etablissement->image_path;
        $newPath = $this->storeImage($validated['photo'], 'establishments/'.$etablissement->id);

        $etablissement->update(['image_path' => $newPath]);
        $this->deleteStoredImage($oldPath);

        return response()->json([
            'data' => [
                'id' => $etablissement->id,
                'image_url' => asset('storage/'.$newPath),
            ],
        ]);
    }

    public function deleteEstablishmentPhoto(int $etablissement): JsonResponse
    {
        $etablissement = Etablissement::findOrFail($etablissement);
        $oldPath = $etablissement->image_path;
        $etablissement->update(['image_path' => null]);
        $this->deleteStoredImage($oldPath);

        return response()->json(['data' => ['id' => $etablissement->id, 'image_url' => null]]);
    }

    public function uploadEstablishmentPhotos(Request $request, int $etablissement): JsonResponse
    {
        $validated = $request->validate([
            'photos' => ['required', 'array', 'min:1', 'max:10'],
            'photos.*' => ['required', 'image', 'max:5120'],
            'titre' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'est_publique' => ['sometimes', 'boolean'],
        ]);
        $establishment = Etablissement::findOrFail($etablissement);
        $photos = [];

        foreach ($validated['photos'] as $image) {
            $path = $this->storeImage($image, 'establishments/'.$etablissement.'/gallery');
            $photo = $establishment->photos()->create([
                'titre' => $validated['titre'] ?? 'Photo',
                'description' => $validated['description'] ?? null,
                'alt_text' => $validated['alt_text'] ?? null,
                'image_path' => $path,
                'est_publique' => $validated['est_publique'] ?? true,
                'status' => 'actif',
            ]);
            $photos[] = $this->establishmentPhotoData($photo);
        }

        return response()->json(['data' => $photos], 201);
    }

    public function updateEstablishmentPhoto(Request $request, int $etablissement, int $photo): JsonResponse
    {
        $galleryPhoto = Etablissement::findOrFail($etablissement)->photos()->findOrFail($photo);
        $validated = $request->validate([
            'image' => ['sometimes', 'required', 'image', 'max:5120'],
            'titre' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'alt_text' => ['sometimes', 'nullable', 'string', 'max:255'],
            'est_publique' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'required', 'in:actif,inactif'],
        ]);

        $oldPath = $galleryPhoto->image_path;
        if (isset($validated['image'])) {
            $validated['image_path'] = $this->storeImage(
                $validated['image'],
                'establishments/'.$etablissement.'/gallery'
            );
            unset($validated['image']);
        }

        $galleryPhoto->update($validated);
        if ($galleryPhoto->image_path !== $oldPath) {
            $this->deleteStoredImage($oldPath);
        }

        return response()->json(['data' => $this->establishmentPhotoData($galleryPhoto)]);
    }

    public function deleteEstablishmentGalleryPhoto(int $etablissement, int $photo): JsonResponse
    {
        $galleryPhoto = Etablissement::findOrFail($etablissement)->photos()->findOrFail($photo);
        $path = $galleryPhoto->image_path;
        $galleryPhoto->delete();
        $this->deleteStoredImage($path);

        return response()->json(['data' => ['id' => $photo, 'deleted' => true]]);
    }

    public function uploadProductImage(Request $request, int $etablissement, Product $product): JsonResponse
    {
        $this->ensureProductBelongsToEstablishment($product, $etablissement);
        $validated = $request->validate([
            'image' => ['required', 'image', 'max:5120'],
        ]);

        $oldPath = $product->image;
        $newPath = $this->storeImage($validated['image'], 'establishments/'.$etablissement.'/products');
        $product->update(['image' => $newPath]);
        $this->deleteStoredImage($oldPath);

        return response()->json(['data' => $this->productImageData($product)]);
    }

    public function deleteProductImage(int $etablissement, Product $product): JsonResponse
    {
        $this->ensureProductBelongsToEstablishment($product, $etablissement);
        $oldPath = $product->image;
        $product->update(['image' => null]);
        $this->deleteStoredImage($oldPath);

        return response()->json(['data' => $this->productImageData($product)]);
    }

    public function products(Request $request, int $etablissement): JsonResponse
    {
        $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
        $query = Product::query()
            ->with(['stock', 'category:id,nom', 'unit:id,name,symbol'])
            ->withSum([
                'reservationItems as pending_reservation_quantity' => fn ($items) => $items
                    ->whereHas('reservation', fn ($reservation) => $reservation->where('statut', 'en_attente')),
            ], 'quantity')
            ->withSum([
                'deliveryItems as pending_delivery_quantity' => fn ($items) => $items
                    ->whereHas('deliveryOrder', fn ($order) => $order->whereIn('statut', [
                        'en_attente', 'payee_partiellement', 'payee',
                    ])),
            ], 'quantity')
            ->where('etablissement_id', $etablissement);

        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->where(function ($builder) use ($term) {
                $builder->where('name', 'like', "%{$term}%")
                    ->orWhere('code', 'like', "%{$term}%")
                    ->orWhere('barcode', 'like', "%{$term}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->boolean('status'));
        }

        $page = $query->orderBy('name')->paginate(max(1, min($request->integer('per_page', 20), 100)));

        return $this->paginated($page, fn (Product $product) => $this->productData($product));
    }

    public function categories(): JsonResponse
    {
        return response()->json([
            'data' => Category::query()
                ->where('status', true)
                ->orderBy('nom')
                ->get(['id', 'nom', 'description'])
                ->map(fn (Category $category) => [
                    'id' => $category->id,
                    'name' => $category->nom,
                    'description' => $category->description,
                ]),
        ]);
    }

    public function units(): JsonResponse
    {
        return response()->json([
            'data' => Unit::query()
                ->where('status', true)
                ->orderBy('name')
                ->get(['id', 'name', 'symbol']),
        ]);
    }

    public function createProduct(Request $request, int $etablissement): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'unit_id' => ['required', 'integer', 'exists:units,id'],
            'code' => [
                'required', 'string', 'max:255',
                Rule::unique('products', 'code')->where('etablissement_id', $etablissement),
            ],
            'barcode' => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'barcode')->where('etablissement_id', $etablissement),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $product = Product::create($validated + ['etablissement_id' => $etablissement]);
        $product->load(['category:id,nom', 'unit:id,name,symbol']);

        return response()->json(['data' => $this->productData($product)], 201);
    }

    public function updateProduct(Request $request, int $etablissement, Product $product): JsonResponse
    {
        abort_unless((int) $product->etablissement_id === $etablissement, 404);

        $validated = $request->validate([
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
            'unit_id' => ['sometimes', 'required', 'integer', 'exists:units,id'],
            'code' => [
                'sometimes', 'required', 'string', 'max:255',
                Rule::unique('products', 'code')->where('etablissement_id', $etablissement)->ignore($product->id),
            ],
            'barcode' => [
                'nullable', 'string', 'max:255',
                Rule::unique('products', 'barcode')->where('etablissement_id', $etablissement)->ignore($product->id),
            ],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['sometimes', 'boolean'],
        ]);

        $product->update($validated);
        $product->load(['stock', 'category:id,nom', 'unit:id,name,symbol']);

        return response()->json(['data' => $this->productData($product)]);
    }

    public function stocks(Request $request, int $etablissement): JsonResponse
    {
        $request->validate([
            'filter' => ['sometimes', Rule::in(['low', 'out'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
        $query = Stock::query()
            ->with(['product.category:id,nom', 'product.unit:id,name,symbol'])
            ->where('etablissement_id', $etablissement);

        if ($request->input('filter') === 'low') {
            $query->whereRaw('quantity <= minimum_stock')->where('quantity', '>', 0);
        } elseif ($request->input('filter') === 'out') {
            $query->where('quantity', '<=', 0);
        }

        $page = $query->orderBy('product_id')->paginate(max(1, min($request->integer('per_page', 20), 100)));

        return $this->paginated($page, fn (Stock $stock) => [
            'id' => $stock->id,
            'product' => $stock->product ? $this->productData($stock->product) : null,
            'quantity' => $stock->quantity,
            'minimum_stock' => $stock->minimum_stock,
            'status' => $stock->quantity <= 0 ? 'out_of_stock' : ($stock->quantity <= $stock->minimum_stock ? 'low' : 'normal'),
            'purchase_price' => $stock->purchase_price,
            'purchase_price_original' => $stock->purchase_price_original,
            'purchase_currency' => $stock->purchase_currency,
            'selling_price' => $stock->selling_price,
            'selling_price_original' => $stock->selling_price_original,
            'selling_currency' => $stock->selling_currency,
        ]);
    }

    public function movements(Request $request, int $etablissement): JsonResponse
    {
        $request->validate([
            'product_id' => ['sometimes', 'integer'],
            'type' => ['sometimes', Rule::in([
                'in', 'out', 'adjust_positive', 'adjust_negative',
                'inventory_in', 'inventory_out', 'stock_in_create', 'stock_in_update',
            ])],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
        $query = Movement::query()
            ->with(['product:id,name,code', 'user:id,name'])
            ->where('etablissement_id', $etablissement);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->integer('product_id'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        $page = $query->latest()->paginate(max(1, min($request->integer('per_page', 20), 100)));

        return $this->paginated($page, fn (Movement $movement) => [
            'id' => $movement->id,
            'product' => $movement->product ? [
                'id' => $movement->product->id,
                'name' => $movement->product->name,
                'code' => $movement->product->code,
            ] : null,
            'type' => $movement->type,
            'quantity' => $movement->quantity,
            'before' => $movement->before,
            'after' => $movement->after,
            'note' => $movement->note,
            'user' => $movement->user?->name,
            'created_at' => $movement->created_at,
        ]);
    }

    public function createMovement(Request $request, int $etablissement): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', Rule::exists('products', 'id')->where('etablissement_id', $etablissement)],
            'type' => ['required', Rule::in(['in', 'out', 'adjust'])],
            'quantity' => ['required', 'integer', 'min:0'],
            'purchase_price' => ['required_if:type,in', 'nullable', 'numeric', 'min:0'],
            'purchase_currency' => ['required_if:type,in', 'nullable', Rule::in(['CDF', 'USD'])],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'selling_currency' => ['nullable', Rule::in(['CDF', 'USD'])],
            'note' => ['required_if:type,adjust', 'nullable', 'string', 'max:500'],
        ]);

        if ($validated['type'] !== 'adjust' && $validated['quantity'] < 1) {
            throw ValidationException::withMessages(['quantity' => 'La quantité doit être supérieure à zéro.']);
        }

        $movement = DB::transaction(function () use ($validated, $etablissement, $request) {
            Etablissement::whereKey($etablissement)->lockForUpdate()->firstOrFail();
            $stock = Stock::where('etablissement_id', $etablissement)
                ->where('product_id', $validated['product_id'])
                ->lockForUpdate()
                ->first();

            if (! $stock && $validated['type'] !== 'in') {
                throw ValidationException::withMessages(['product_id' => 'Aucun stock n’existe pour ce produit.']);
            }

            $oldQuantity = (int) ($stock->quantity ?? 0);
            $purchasePrice = (float) ($stock->purchase_price ?? 0);
            $purchaseOriginal = $stock->purchase_price_original ?? $purchasePrice;
            $purchaseCurrency = $stock->purchase_currency ?? 'CDF';
            $purchaseRate = $stock->purchase_exchange_rate;
            $sellingPrice = (float) ($stock->selling_price ?? 0);
            $sellingOriginal = $stock->selling_price_original ?? $sellingPrice;
            $sellingCurrency = $stock->selling_currency ?? 'CDF';
            $sellingRate = $stock->selling_exchange_rate;

            if ($validated['type'] === 'in') {
                [$newPurchasePrice, $newPurchaseRate] = PurchasePrice::normalize(
                    (float) $validated['purchase_price'],
                    $validated['purchase_currency']
                );
                $newQuantity = $oldQuantity + $validated['quantity'];
                $purchasePrice = $newQuantity > 0
                    ? (($oldQuantity * $purchasePrice) + ($validated['quantity'] * $newPurchasePrice)) / $newQuantity
                    : $newPurchasePrice;
                $purchaseOriginal = $validated['purchase_price'];
                $purchaseCurrency = $validated['purchase_currency'];
                $purchaseRate = $newPurchaseRate;

                if (isset($validated['selling_price'])) {
                    $sellingCurrency = $validated['selling_currency'] ?? 'CDF';
                    [$sellingPrice, $sellingRate] = PurchasePrice::normalize(
                        (float) $validated['selling_price'],
                        $sellingCurrency,
                        'selling_currency'
                    );
                    $sellingOriginal = $validated['selling_price'];
                }

                $stock = Stock::updateOrCreate(
                    ['etablissement_id' => $etablissement, 'product_id' => $validated['product_id']],
                    [
                        'quantity' => $newQuantity,
                        'purchase_price' => $purchasePrice,
                        'purchase_price_original' => $purchaseOriginal,
                        'purchase_currency' => $purchaseCurrency,
                        'purchase_exchange_rate' => $purchaseRate,
                        'selling_price' => $sellingPrice,
                        'selling_price_original' => $sellingOriginal,
                        'selling_currency' => $sellingCurrency,
                        'selling_exchange_rate' => $sellingRate,
                        'minimum_stock' => $stock->minimum_stock ?? 0,
                    ]
                );
                $type = 'in';
                $quantity = $validated['quantity'];
            } elseif ($validated['type'] === 'out') {
                if ($oldQuantity < $validated['quantity']) {
                    throw ValidationException::withMessages(['quantity' => 'La quantité disponible est insuffisante.']);
                }
                $stock->quantity = $oldQuantity - $validated['quantity'];
                $stock->save();
                $type = 'out';
                $quantity = $validated['quantity'];
            } else {
                $difference = $validated['quantity'] - $oldQuantity;
                if ($difference === 0) {
                    throw ValidationException::withMessages(['quantity' => 'La quantité indiquée est identique au stock actuel.']);
                }
                $stock->quantity = $validated['quantity'];
                $stock->save();
                $type = $difference > 0 ? 'adjust_positive' : 'adjust_negative';
                $quantity = abs($difference);
            }

            return Movement::create([
                'etablissement_id' => $etablissement,
                'product_id' => $validated['product_id'],
                'type' => $type,
                'quantity' => $quantity,
                'before' => $oldQuantity,
                'after' => $stock->quantity,
                'purchase_price' => $purchasePrice,
                'purchase_price_original' => $purchaseOriginal,
                'purchase_currency' => $purchaseCurrency,
                'purchase_exchange_rate' => $purchaseRate,
                'selling_price' => $sellingPrice,
                'selling_price_original' => $sellingOriginal,
                'selling_currency' => $sellingCurrency,
                'selling_exchange_rate' => $sellingRate,
                'note' => $validated['note'] ?? ($type === 'in' ? 'Approvisionnement' : 'Sortie de stock'),
                'user_id' => $request->user()->id,
            ]);
        });

        return response()->json(['data' => $movement], 201);
    }

    public function createInventory(Request $request, int $etablissement): JsonResponse
    {
        $validated = $request->validate([
            'inventory_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => [
                'required', 'integer', 'distinct',
                Rule::exists('products', 'id')->where('etablissement_id', $etablissement),
            ],
            'items.*.physical_quantity' => ['required', 'integer', 'min:0'],
        ]);

        $inventory = DB::transaction(function () use ($validated, $etablissement, $request) {
            Etablissement::whereKey($etablissement)->lockForUpdate()->firstOrFail();
            $inventory = Inventory::create([
                'etablissement_id' => $etablissement,
                'inventory_date' => $validated['inventory_date'],
                'status' => 'pending',
                'user_id' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $stock = Stock::where('etablissement_id', $etablissement)
                    ->where('product_id', $item['product_id'])
                    ->lockForUpdate()
                    ->firstOrFail();
                $difference = $item['physical_quantity'] - $stock->quantity;

                $inventory->items()->create([
                    'stock_id' => $stock->id,
                    'system_quantity' => $stock->quantity,
                    'physical_quantity' => $item['physical_quantity'],
                    'difference' => $difference,
                ]);

                if ($difference !== 0) {
                    $before = $stock->quantity;
                    $stock->quantity = $item['physical_quantity'];
                    $stock->save();
                    Movement::create([
                        'etablissement_id' => $etablissement,
                        'product_id' => $stock->product_id,
                        'type' => $difference > 0 ? 'inventory_in' : 'inventory_out',
                        'quantity' => abs($difference),
                        'before' => $before,
                        'after' => $stock->quantity,
                        'purchase_price' => $stock->purchase_price,
                        'selling_price' => $stock->selling_price,
                        'note' => 'Inventaire physique #'.$inventory->id,
                        'user_id' => $request->user()->id,
                    ]);
                }
            }

            $inventory->update(['status' => 'completed']);

            return $inventory->load('items');
        });

        return response()->json(['data' => $inventory], 201);
    }

    public function dashboard(int $etablissement): JsonResponse
    {
        $stats = Stock::where('etablissement_id', $etablissement)
            ->selectRaw('COUNT(*) as product_count')
            ->selectRaw('COALESCE(SUM(quantity), 0) as total_quantity')
            ->selectRaw('COALESCE(SUM(quantity * purchase_price), 0) as purchase_value')
            ->selectRaw('COALESCE(SUM(quantity * selling_price), 0) as selling_value')
            ->selectRaw('SUM(CASE WHEN quantity > 0 AND quantity <= minimum_stock THEN 1 ELSE 0 END) as low_stock_count')
            ->selectRaw('SUM(CASE WHEN quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock_count')
            ->first();

        return response()->json([
            'data' => [
                'stats' => $stats,
                'recent_movements' => Movement::with('product:id,name,code')
                    ->where('etablissement_id', $etablissement)
                    ->latest()
                    ->limit(10)
                    ->get(),
            ],
        ]);
    }

    public function serviceReservations(Request $request, int $etablissement): JsonResponse
    {
        $request->validate([
            'status' => ['sometimes', Rule::in(['en_attente', 'confirmé', 'rejeté'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
        $page = Reservation::with('service:id,nom,etablissement_id')
            ->whereHas('service', fn ($query) => $query->where('etablissement_id', $etablissement))
            ->when($request->filled('status'), fn ($query) => $query->where('statut', $request->string('status')->toString()))
            ->latest()
            ->paginate(max(1, min($request->integer('per_page', 20), 100)));

        return $this->paginated($page, fn (Reservation $item) => [
            'id' => $item->id,
            'service' => $item->service?->nom,
            'client_name' => $item->client_name,
            'client_phone' => $item->client_phone,
            'date' => $item->date,
            'status' => $item->statut,
            'created_at' => $item->created_at,
        ]);
    }

    public function updateServiceReservation(Request $request, int $etablissement, int $reservation): JsonResponse
    {
        $validated = $request->validate(['status' => ['required', Rule::in(['confirmé', 'rejeté'])]]);
        $item = Reservation::whereKey($reservation)
            ->whereHas('service', fn ($query) => $query->where('etablissement_id', $etablissement))
            ->firstOrFail();

        if ($item->statut !== 'en_attente') {
            throw ValidationException::withMessages(['status' => 'Cette demande a déjà été traitée.']);
        }

        $item->update(['statut' => $validated['status']]);

        return response()->json(['data' => ['id' => $item->id, 'status' => $item->statut]]);
    }

    public function productReservations(Request $request, int $etablissement): JsonResponse
    {
        $request->validate([
            'status' => ['sometimes', Rule::in(['en_attente', 'confirmé', 'rejeté'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
        $page = ProductReservation::with('items.product:id,name,code')
            ->where('etablissement_id', $etablissement)
            ->when($request->filled('status'), fn ($query) => $query->where('statut', $request->string('status')->toString()))
            ->latest()
            ->paginate(max(1, min($request->integer('per_page', 20), 100)));

        return $this->paginated($page, fn (ProductReservation $item) => [
            'id' => $item->id,
            'client_name' => $item->client_name,
            'client_phone' => $item->client_phone,
            'message' => $item->message,
            'status' => $item->statut,
            'items' => $item->items->map(fn ($line) => [
                'product_id' => $line->product_id,
                'product_name' => $line->product?->name,
                'quantity' => $line->quantity,
            ]),
            'created_at' => $item->created_at,
        ]);
    }

    public function updateProductReservation(Request $request, int $etablissement, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['confirmed', 'rejected'])],
        ]);
        $newStatus = $validated['status'] === 'confirmed' ? 'confirmé' : 'rejeté';
        $reservation = ProductReservation::where('etablissement_id', $etablissement)
            ->findOrFail($id);

        DB::transaction(function () use ($reservation, $newStatus, $request) {
            $reservation = ProductReservation::whereKey($reservation->id)
                ->lockForUpdate()
                ->with('items')
                ->firstOrFail();

            if ($reservation->statut !== 'en_attente') {
                throw ValidationException::withMessages([
                    'status' => 'Cette demande a déjà été traitée.',
                ]);
            }

            if ($newStatus === 'confirmé') {
                foreach ($reservation->items as $item) {
                    $stock = Stock::where('etablissement_id', $reservation->etablissement_id)
                        ->where('product_id', $item->product_id)
                        ->lockForUpdate()
                        ->first();

                    if (! $stock || $stock->quantity < $item->quantity) {
                        throw ValidationException::withMessages([
                            'status' => 'Le stock actuel ne permet plus de confirmer cette demande.',
                        ]);
                    }

                    $before = $stock->quantity;
                    $stock->quantity -= $item->quantity;
                    $stock->save();
                    Movement::create([
                        'etablissement_id' => $reservation->etablissement_id,
                        'product_id' => $item->product_id,
                        'type' => 'out',
                        'quantity' => $item->quantity,
                        'before' => $before,
                        'after' => $stock->quantity,
                        'purchase_price' => $stock->purchase_price,
                        'selling_price' => $stock->selling_price,
                        'note' => 'Réservation article #'.$reservation->id,
                        'user_id' => $request->user()->id,
                    ]);
                }
            }

            $reservation->update(['statut' => $newStatus]);
        });

        return response()->json([
            'data' => ['id' => $reservation->id, 'status' => $newStatus],
        ]);
    }

    public function deliveryOrders(Request $request, int $etablissement): JsonResponse
    {
        $request->validate([
            'status' => ['sometimes', Rule::in([
                'en_attente', 'payee_partiellement', 'payee', 'en_livraison', 'livree',
            ])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
        $page = DeliveryOrder::with('items')
            ->where('etablissement_id', $etablissement)
            ->when($request->filled('status'), fn ($query) => $query->where('statut', $request->string('status')->toString()))
            ->latest()
            ->paginate(max(1, min($request->integer('per_page', 20), 100)));

        return $this->paginated($page, fn (DeliveryOrder $item) => [
            'id' => $item->id,
            'client_name' => $item->client_name,
            'client_phone' => $item->client_phone,
            'delivery_address' => $item->delivery_address,
            'payment_plan' => $item->payment_plan,
            'total_amount' => $item->total_amount,
            'deposit_amount' => $item->deposit_amount,
            'amount_paid' => $item->amount_paid,
            'status' => $item->statut,
            'delivered_at' => $item->delivered_at,
            'items' => $item->items,
            'created_at' => $item->created_at,
        ]);
    }

    public function saleProducts(Request $request, int $etablissement): JsonResponse
    {
        $request->validate(['search' => ['sometimes', 'string', 'max:255']]);
        $request->merge(['etablissement_id' => $etablissement]);
        $query = Product::with(['stock', 'unit:id,name,symbol', 'category:id,nom'])
            ->withSum([
                'reservationItems as pending_reservation_quantity' => fn ($items) => $items
                    ->whereHas('reservation', fn ($reservation) => $reservation->where('statut', 'en_attente')),
            ], 'quantity')
            ->withSum([
                'deliveryItems as pending_delivery_quantity' => fn ($items) => $items
                    ->whereHas('deliveryOrder', fn ($order) => $order->whereIn('statut', [
                        'en_attente', 'payee_partiellement', 'payee',
                    ])),
            ], 'quantity')
            ->where('etablissement_id', $etablissement)
            ->where('status', true)
            ->whereHas('stock', fn ($stock) => $stock->where('quantity', '>', 0));

        if ($request->filled('search')) {
            $term = $request->string('search')->toString();
            $query->where(fn ($builder) => $builder
                ->where('name', 'like', "%{$term}%")
                ->orWhere('code', 'like', "%{$term}%")
                ->orWhere('barcode', 'like', "%{$term}%"));
        }

        return response()->json([
            'data' => $query->orderBy('name')->limit(20)->get()
                ->map(fn (Product $product) => $this->productData($product)),
            'meta' => [
                'tva_rates' => [0, 5.5, 10, 20],
            ],
        ]);
    }

    public function createSale(Request $request, int $etablissement): JsonResponse
    {
        $request->merge(['etablissement_id' => $etablissement]);
        $result = app(SaleController::class)->store($request);
        $payload = $result->getData(true);

        if ($result->getStatusCode() >= 400) {
            return $result;
        }

        return response()->json([
            'data' => [
                'id' => $payload['order_id'],
                'order_number' => $payload['order_number'],
                'total_ht' => $payload['total_ht'],
                'total_tva' => $payload['total_tva'],
                'initial_total' => $payload['initial_total'],
                'discount_amount' => $payload['discount_amount'],
                'total_ttc' => $payload['total_ttc'],
                'payment_currency' => $payload['payment_currency'],
                'payment_amount' => $payload['payment_amount'],
                'invoice_url' => $payload['invoice_url'],
            ],
            'message' => $payload['message'],
        ], $result->getStatusCode());
    }

    public function sales(Request $request, int $etablissement): JsonResponse
    {
        $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'date_from' => ['sometimes', 'date'],
            'date_to' => ['sometimes', 'date'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);
        $page = \App\Models\Order::with(['items.product:id,name,code', 'user:id,name', 'customer:id,name'])
            ->where('etablissement_id', $etablissement)
            ->whereIn('type', ['pos', 'direct'])
            ->when($request->filled('search'), fn ($query) => $query->where('order_number', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->date('date_to')))
            ->latest()
            ->paginate(max(1, min($request->integer('per_page', 20), 100)));

        return $this->paginated($page, fn ($order) => [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'payment_currency' => $order->payment_currency,
            'payment_amount' => $order->payment_amount,
            'total_ht' => $order->total_ht,
            'total_tva' => $order->total_tva,
            'total_amount' => $order->total_amount,
            'created_at' => $order->created_at,
            'items' => $order->items->map(fn ($line) => [
                'product_id' => $line->product_id,
                'product_name' => $line->product?->name,
                'quantity' => $line->quantity,
                'price_ht' => $line->price_ht,
                'subtotal_ttc' => $line->subtotal_ttc,
            ]),
        ]);
    }

    private function establishmentData(Etablissement $item, bool $detail = false): array
    {
        $data = [
            'id' => $item->id,
            'name' => $item->nom,
            'slug' => $item->slug,
            'description' => $item->description,
            'type' => $item->typeEtablissement?->nom,
            'phone' => $item->telephone,
            'whatsapp_number' => $item->whatsapp_number,
            'email' => $item->email,
            'website' => $item->website,
            'social' => [
                'facebook' => $item->facebook,
                'twitter' => $item->twitter,
                'instagram' => $item->instagram,
            ],
            'opening_hours' => [
                'opens_at' => $item->horaires_ouverture,
                'closes_at' => $item->horaires_fermeture,
                'days' => $item->jours_ouverture,
            ],
            'address' => [
                'city' => $item->ville,
                'commune' => $item->commune,
                'neighborhood' => $item->quartier,
                'avenue' => $item->avenue,
                'number' => $item->numero,
                'latitude' => $item->latitude === null ? null : (float) $item->latitude,
                'longitude' => $item->longitude === null ? null : (float) $item->longitude,
                'directions' => $item->itineraire,
            ],
            'rating' => $item->note_moyenne,
            'image_url' => $item->image_path ? asset('storage/'.$item->image_path) : null,
        ];

        if ($detail) {
            $data['services'] = $item->services->where('disponibilite', true)->values()->map(fn (Service $service) => [
                'id' => $service->id,
                'name' => $service->nom,
                'description' => $service->description,
                'price' => $service->prix,
                'duration' => $service->duree,
            ]);
            $data['photos'] = $item->photos->map(fn ($photo) => [
                'id' => $photo->id,
                'description' => $photo->description,
                'image_url' => asset('storage/'.$photo->image_path),
            ]);
            $data['promotions'] = $item->promotions->map(fn ($promotion) => [
                'id' => $promotion->id,
                'title' => $promotion->titre,
                'description' => $promotion->description,
                'discount' => $promotion->reduction,
                'discount_type' => $promotion->type_reduction,
                'starts_at' => $promotion->date_debut,
                'ends_at' => $promotion->date_fin,
                'conditions' => $promotion->conditions,
            ]);
            $data['advertisements'] = $item->publicites->map(fn ($advertisement) => [
                'id' => $advertisement->id,
                'title' => $advertisement->titre,
                'description' => $advertisement->description,
                'image_url' => asset('storage/'.$advertisement->image_path),
                'starts_on' => $advertisement->date,
                'duration_days' => $advertisement->dure,
            ]);
        }

        return $data;
    }

    private function productData(Product $product): array
    {
        return [
            'id' => $product->id,
            'code' => $product->code,
            'barcode' => $product->barcode,
            'name' => $product->name,
            'description' => $product->description,
            'image_url' => $product->image ? asset('storage/'.$product->image) : null,
            'status' => $product->status,
            'category' => $product->category?->nom,
            'unit' => $product->unit ? [
                'name' => $product->unit->name,
                'symbol' => $product->unit->symbol,
            ] : null,
            'stock' => $product->stock ? [
                'quantity' => $product->stock->quantity,
                'available_quantity' => max(
                    0,
                    $product->stock->quantity
                        - (int) ($product->pending_reservation_quantity ?? 0)
                        - (int) ($product->pending_delivery_quantity ?? 0)
                ),
                'minimum_stock' => $product->stock->minimum_stock,
                'selling_price' => $product->stock->selling_price,
                'selling_price_original' => $product->stock->selling_price_original,
                'selling_currency' => $product->stock->selling_currency,
            ] : null,
        ];
    }

    private function establishmentPhotoData(Photo $photo): array
    {
        return [
            'id' => $photo->id,
            'title' => $photo->titre,
            'description' => $photo->description,
            'alt_text' => $photo->alt_text,
            'image_url' => asset('storage/'.$photo->image_path),
        ];
    }

    private function productImageData(Product $product): array
    {
        return [
            'id' => $product->id,
            'image_url' => $product->image ? asset('storage/'.$product->image) : null,
        ];
    }

    private function ensureProductBelongsToEstablishment(Product $product, int $etablissement): void
    {
        abort_unless((int) $product->etablissement_id === $etablissement, 404);
    }

    private function storeImage(UploadedFile $image, string $directory): string
    {
        $path = $image->store($directory, 'public');
        if ($path === false) {
            throw new RuntimeException('Impossible d’enregistrer le fichier image.');
        }

        return $path;
    }

    private function deleteStoredImage(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path) && ! Storage::disk('public')->delete($path)) {
            throw new RuntimeException('Impossible de supprimer le fichier image.');
        }
    }

    private function paginated($page, callable $transform): JsonResponse
    {
        return response()->json([
            'data' => $page->getCollection()->map($transform)->values(),
            'meta' => [
                'current_page' => $page->currentPage(),
                'per_page' => $page->perPage(),
                'total' => $page->total(),
                'last_page' => $page->lastPage(),
            ],
        ]);
    }
}
