<?php

namespace App\Http\Controllers;

use App\Models\Etablissement;
use Illuminate\Http\Request;

use App\Models\Reservation;
use App\Models\Service;
use App\Models\Product;
use App\Models\ProductReservation;
use App\Models\ProductReservationItem;
use App\Models\Stock;
use App\Models\Movement;
use App\Models\DeliveryOrder;
use App\Models\DeliveryOrderItem;
use App\Mail\DeliveryOrderReceived;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{



public function index()
{
    try{
    $etablissements = Etablissement::whereHas('users', function($query) {
        $query->where('user_id', auth()->id());
    })->with(['photos', 'services'])->paginate(5);
    $etablissementIds = Etablissement::whereHas('users', function ($q) {
        $q->where('users.id', auth()->id());
    })
    ->where('statut', '=', 'actif')
    ->pluck('id');



$serviceIds = Service::whereIn('etablissement_id', $etablissementIds)->pluck('id');
Reservation::whereIn('service_id', $serviceIds)
    ->where('created_at', '<', now()->subHours(48))
    ->delete();


$reservations = Reservation::with(['service', 'service.etablissement'])
    ->whereIn('service_id', $serviceIds)
    ->where('created_at', '>=', now()->subHours(48)) // garde seulement les < 48h
    ->latest()
    ->paginate(10);

$productReservations = ProductReservation::with(['etablissement', 'items.product'])
    ->whereIn('etablissement_id', $etablissementIds)
    ->latest()
    ->paginate(10, ['*'], 'article_page');

$deliveryOrders = DeliveryOrder::with(['etablissement', 'items'])
    ->whereIn('etablissement_id', $etablissementIds)
    ->latest()
    ->paginate(10, ['*'], 'delivery_page');

    $etablissementIds = Etablissement::whereHas('users', function ($q) {
        $q->where('users.id', auth()->id());
    })
    ->where('statut', '=', 'actif')
    ->pluck('id');

        $services = Service::where('etablissement_id', $etablissementIds)->get();

    return view('etablissements.reservation.index', compact('etablissements', 'reservations', 'services', 'productReservations', 'deliveryOrders'));}
    catch(\Throwable $e){
        report($e);
        return redirect()->back()->with('error', 'Impossible de charger les réservations. Réessayez plus tard.');
    }

}
public function changerStatut(Request $request, $id)
{
    $request->validate([
        'statut' => 'required|in:confirmé,rejeté'
    ]);

    $reservation = Reservation::findOrFail($id);
    $reservation->statut = $request->statut;
    $reservation->save();

    return response()->json(['message' => "Réservation mise à jour : {$request->statut}."]);
}

public function store(Request $request)
{

    $request->validate([
        'service_id' => 'required|integer|exists:services,id',
        'date' => 'required|date|after_or_equal:today',
        'client_name' => 'required|string|max:100',
        'client_phone' => 'nullable|string|max:25',
        //'statut' => 'in:en_attente,confirme'
    ]);

    // Vérifier si la date est déjà réservée (optionnel)
  $existe = Reservation::where('service_id', $request->service_id)
    ->where('date', $request->date)
    ->where('statut', 'confirmé')
    ->exists();

if ($existe) {
    return response()->json(['message' => 'Cette date est déjà confirmée.'], 409);
}


    Reservation::create([
        'service_id' => $request->service_id,
        'client_name' => $request->client_name,
        'client_phone' => $request->client_phone,
        'statut' => $request->statut ?? 'en_attente', // valeur par défaut si non fourni
        'date' => $request->date
    ]);

    return response()->json(['message' => 'Réservation créée avec succès.']);
}

public function storeArticles(Request $request)
{
    $validated = $request->validate([
        'etablissement_id' => 'required|exists:etablissements,id',
        'client_name' => 'required|string|max:100',
        'client_phone' => 'required|string|max:25',
        'message' => 'nullable|string|max:1000',
        'product_ids' => 'required|array|min:1',
        'product_ids.*' => 'required|integer|distinct|exists:products,id',
        'quantities' => 'required|array',
        'quantities.*' => 'required|integer|min:1',
    ]);

    $etablissement = Etablissement::where('statut', 'actif')->findOrFail($validated['etablissement_id']);
    $selectedProductIds = collect($validated['product_ids'])->map(fn ($id) => (int) $id)->unique()->values();

    DB::transaction(function () use ($request, $validated, $etablissement, $selectedProductIds) {
        Etablissement::whereKey($etablissement->id)->lockForUpdate()->firstOrFail();

        $products = Product::with('stock')
            ->whereIn('id', $selectedProductIds)
            ->where('etablissement_id', $etablissement->id)
            ->where('status', true)
            ->orderBy('id')
            ->get();

        if ($products->count() !== $selectedProductIds->count()) {
            throw ValidationException::withMessages([
                'product_ids' => 'Un ou plusieurs articles ne sont plus disponibles dans cet établissement.',
            ]);
        }

        foreach ($products as $product) {
            $quantity = (int) ($validated['quantities'][$product->id] ?? 0);
            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'quantities' => 'Indiquez une quantité valide pour chaque article sélectionné.',
                ]);
            }

            $stock = Stock::where('etablissement_id', $etablissement->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();
            $physicalQuantity = (int) ($stock->quantity ?? 0);
            $pendingQuantity = ProductReservationItem::where('product_id', $product->id)
                ->whereHas('reservation', fn ($query) => $query->where('statut', 'en_attente'))
                ->sum('quantity');
            $pendingDeliveryQuantity = DeliveryOrderItem::where('product_id', $product->id)
                ->whereHas('deliveryOrder', fn ($query) => $query->whereIn('statut', ['en_attente', 'payee_partiellement', 'payee']))
                ->sum('quantity');

            if ($quantity > max(0, $physicalQuantity - $pendingQuantity - $pendingDeliveryQuantity)) {
                throw ValidationException::withMessages([
                    'quantities' => 'La quantité demandée pour « ' . $product->name . ' » n’est plus disponible.',
                ]);
            }
        }

        $reservation = ProductReservation::create([
            'etablissement_id' => $etablissement->id,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'],
            'message' => $validated['message'] ?? null,
            'statut' => 'en_attente',
        ]);

        foreach ($products as $product) {
            $reservation->items()->create([
                'product_id' => $product->id,
                'quantity' => (int) $validated['quantities'][$product->id],
            ]);
        }
    });

    return redirect()->route('ets.info', $etablissement->id)->with('success', 'Votre demande de réservation a été envoyée à l’établissement.');
}

public function storeDelivery(Request $request)
{
    $validated = $request->validate([
        'etablissement_id' => 'required|exists:etablissements,id',
        'client_name' => 'required|string|max:100',
        'client_phone' => 'required|string|max:25',
        'delivery_address' => 'required|string|max:500',
        'payment_plan' => 'required|in:one_time,two_installments',
        'deposit_amount' => 'nullable|numeric|min:0.01|required_if:payment_plan,two_installments',
        'product_ids' => 'required|array|min:1',
        'product_ids.*' => 'required|integer|distinct|exists:products,id',
        'quantities' => 'required|array',
        'quantities.*' => 'required|integer|min:1',
    ]);

    $etablissement = Etablissement::where('statut', 'actif')->findOrFail($validated['etablissement_id']);
    $productIds = collect($validated['product_ids'])->map(fn ($id) => (int) $id)->unique()->values();

    $deliveryOrder = DB::transaction(function () use ($validated, $etablissement, $productIds) {
        Etablissement::whereKey($etablissement->id)->lockForUpdate()->firstOrFail();
        $products = Product::with('stock')
            ->whereIn('id', $productIds)
            ->where('etablissement_id', $etablissement->id)
            ->where('status', true)
            ->orderBy('id')
            ->get();

        if ($products->count() !== $productIds->count()) {
            throw ValidationException::withMessages([
                'product_ids' => 'Un ou plusieurs articles ne sont plus disponibles dans cet établissement.',
            ]);
        }

        $totalAmount = 0;
        $lines = [];
        foreach ($products as $product) {
            $quantity = (int) ($validated['quantities'][$product->id] ?? 0);
            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'quantities' => 'Indiquez une quantité valide pour chaque article choisi.',
                ]);
            }

            $stock = Stock::where('etablissement_id', $etablissement->id)
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();
            $physicalQuantity = (int) ($stock->quantity ?? 0);
            $pendingReservation = ProductReservationItem::where('product_id', $product->id)
                ->whereHas('reservation', fn ($query) => $query->where('statut', 'en_attente'))
                ->sum('quantity');
            $pendingDelivery = DeliveryOrderItem::where('product_id', $product->id)
                ->whereHas('deliveryOrder', fn ($query) => $query->whereIn('statut', ['en_attente', 'payee_partiellement', 'payee']))
                ->sum('quantity');
            $available = max(0, $physicalQuantity - $pendingReservation - $pendingDelivery);

            if ($quantity > $available) {
                throw ValidationException::withMessages([
                    'quantities' => 'La quantité demandée pour « ' . $product->name . ' » n’est plus disponible.',
                ]);
            }

            $unitPrice = (float) ($stock->selling_price ?? 0);
            $lineTotal = round($unitPrice * $quantity, 2);
            $totalAmount += $lineTotal;
            $lines[] = [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'product_code' => $product->code,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
            ];
        }

        $totalAmount = round($totalAmount, 2);
        $depositAmount = $validated['payment_plan'] === 'one_time'
            ? $totalAmount
            : round((float) $validated['deposit_amount'], 2);

        if ($depositAmount > $totalAmount) {
            throw ValidationException::withMessages([
                'deposit_amount' => 'L’acompte ne peut pas dépasser le total de la commande.',
            ]);
        }

        $order = DeliveryOrder::create([
            'etablissement_id' => $etablissement->id,
            'client_name' => $validated['client_name'],
            'client_phone' => $validated['client_phone'],
            'delivery_address' => $validated['delivery_address'],
            'payment_plan' => $validated['payment_plan'],
            'total_amount' => $totalAmount,
            'deposit_amount' => $depositAmount,
            'amount_paid' => 0,
            'statut' => 'en_attente',
        ]);
        $order->items()->createMany($lines);

        return $order->load('etablissement', 'items');
    });

    $recipients = $deliveryOrder->etablissement->users()->pluck('email')
        ->push($deliveryOrder->etablissement->email)
        ->filter()
        ->unique()
        ->values()
        ->all();

    if ($recipients !== []) {
        try {
            Mail::to($recipients)->send(new DeliveryOrderReceived($deliveryOrder));
        } catch (\Throwable $exception) {
            report($exception);
        }
    }

    return redirect()->route('welcome')->with('success', 'Votre demande de livraison a été transmise à l’établissement.');
}

public function manageDelivery(Request $request, $id)
{
    $validated = $request->validate([
        'action' => 'required|in:confirm_payment,confirm_balance,start_delivery,mark_delivered',
    ]);
    $etablissementIds = Etablissement::whereHas('users', function ($query) {
        $query->where('users.id', Auth::id());
    })->where('statut', 'actif')->pluck('id');

    $order = DeliveryOrder::whereIn('etablissement_id', $etablissementIds)->findOrFail($id);

    DB::transaction(function () use ($order, $validated) {
        $order = DeliveryOrder::whereKey($order->id)
            ->lockForUpdate()
            ->with('items')
            ->firstOrFail();

        if ($validated['action'] === 'confirm_payment') {
            if ($order->statut !== 'en_attente') {
                throw ValidationException::withMessages(['action' => 'Cette commande ne peut plus recevoir son premier paiement.']);
            }

            $order->amount_paid = $order->payment_plan === 'one_time'
                ? $order->total_amount
                : min($order->deposit_amount, $order->total_amount);
            $order->statut = $order->amount_paid >= $order->total_amount ? 'payee' : 'payee_partiellement';
        } elseif ($validated['action'] === 'confirm_balance') {
            if ($order->statut !== 'payee_partiellement') {
                throw ValidationException::withMessages(['action' => 'Aucun solde ne reste à confirmer.']);
            }

            $order->amount_paid = $order->total_amount;
            $order->statut = 'payee';
        } elseif ($validated['action'] === 'start_delivery') {
            if ($order->statut !== 'payee') {
                throw ValidationException::withMessages(['action' => 'Le paiement complet doit être confirmé avant le départ en livraison.']);
            }

            foreach ($order->items as $item) {
                $stock = Stock::where('etablissement_id', $order->etablissement_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();
                $physicalQuantity = (int) ($stock->quantity ?? 0);
                $pendingProductReservations = ProductReservationItem::where('product_id', $item->product_id)
                    ->whereHas('reservation', fn ($query) => $query->where('statut', 'en_attente'))
                    ->sum('quantity');
                $otherDeliveryOrders = DeliveryOrderItem::where('product_id', $item->product_id)
                    ->where('delivery_order_id', '!=', $order->id)
                    ->whereHas('deliveryOrder', fn ($query) => $query->whereIn('statut', ['en_attente', 'payee_partiellement', 'payee']))
                    ->sum('quantity');
                $available = max(0, $physicalQuantity - $pendingProductReservations - $otherDeliveryOrders);

                if (!$stock || $item->quantity > $available) {
                    throw ValidationException::withMessages([
                        'action' => 'Le stock disponible ne permet plus d’expédier tous les articles de cette commande.',
                    ]);
                }

                $before = $stock->quantity;
                $stock->quantity -= $item->quantity;
                $stock->save();

                Movement::create([
                    'etablissement_id' => $order->etablissement_id,
                    'product_id' => $item->product_id,
                    'type' => 'out',
                    'quantity' => $item->quantity,
                    'before' => $before,
                    'after' => $stock->quantity,
                    'purchase_price' => $stock->purchase_price,
                    'selling_price' => $item->unit_price,
                    'note' => 'Commande livraison #' . $order->id,
                    'user_id' => Auth::id(),
                ]);
            }

            $order->statut = 'en_livraison';
        } else {
            if ($order->statut !== 'en_livraison') {
                throw ValidationException::withMessages(['action' => 'La commande doit être en livraison avant d’être marquée livrée.']);
            }

            $order->statut = 'livree';
            $order->delivered_at = now();
        }

        $order->save();
    });

    return back()->with('success', match ($validated['action']) {
        'confirm_payment' => 'Le paiement reçu a été enregistré.',
        'confirm_balance' => 'Le solde a été enregistré; la commande est payée.',
        'start_delivery' => 'La commande est en livraison et le stock a été mis à jour.',
        'mark_delivered' => 'La commande a été marquée comme livrée.',
    });
}

public function changerStatutArticles(Request $request, $id)
{
    $validated = $request->validate([
        'statut' => 'required|in:confirmé,rejeté',
    ]);
    $etablissementIds = Etablissement::whereHas('users', function ($query) {
        $query->where('users.id', Auth::id());
    })->where('statut', 'actif')->pluck('id');

    $reservation = ProductReservation::whereIn('etablissement_id', $etablissementIds)->findOrFail($id);
    if ($reservation->statut !== 'en_attente') {
        return back()->with('error', 'Cette demande a déjà été traitée.');
    }

    DB::transaction(function () use ($reservation, $validated) {
        $reservation = ProductReservation::whereKey($reservation->id)->lockForUpdate()->with('items.product')->firstOrFail();

        if ($validated['statut'] === 'confirmé') {
            foreach ($reservation->items as $item) {
                $stock = Stock::where('etablissement_id', $reservation->etablissement_id)
                    ->where('product_id', $item->product_id)
                    ->lockForUpdate()
                    ->first();

                if (!$stock || $stock->quantity < $item->quantity) {
                    throw ValidationException::withMessages([
                        'statut' => 'Le stock actuel ne permet plus de confirmer cette demande.',
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
                    'note' => 'Réservation article #' . $reservation->id,
                    'user_id' => Auth::id(),
                ]);
            }
        }

        $reservation->statut = $validated['statut'];
        $reservation->save();
    });

    return back()->with('success', $validated['statut'] === 'confirmé'
        ? 'Réservation confirmée et stock mis à jour.'
        : 'Réservation refusée; les quantités sont de nouveau disponibles.');
}

}
