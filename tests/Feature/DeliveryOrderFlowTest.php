<?php

use App\Mail\DeliveryOrderReceived;
use App\Models\Abonnement;
use App\Models\Category;
use App\Models\DeliveryOrder;
use App\Models\Etablissement;
use App\Models\Movement;
use App\Models\Product;
use App\Models\ProductReservationItem;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

function makeDeliveryOrderTestContext(int $quantity = 10): array
{
    $user = User::factory()->gerant()->create();
    $etablissement = Etablissement::factory()->create([
        'statut' => 'actif',
        'email' => $user->email,
    ]);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Abonnement::create([
        'etablissement_id' => $etablissement->id,
        'date_debut' => today()->subDay(),
        'date_fin' => today()->addDays(30),
        'statut' => 'actif',
        'type_operation' => 'creation',
        'motif' => 'Test livraison',
        'created_by' => $user->id,
    ]);

    $category = Category::create([
        'nom' => 'Livraison ' . $etablissement->id,
        'description' => 'Test',
        'status' => true,
    ]);
    $unit = Unit::create([
        'name' => 'Unité livraison ' . $etablissement->id,
        'symbol' => 'u',
        'status' => true,
    ]);
    $product = Product::create([
        'etablissement_id' => $etablissement->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'DLV-' . $etablissement->id,
        'name' => 'Produit livraison',
        'status' => true,
    ]);
    $stock = Stock::create([
        'etablissement_id' => $etablissement->id,
        'product_id' => $product->id,
        'quantity' => $quantity,
        'minimum_stock' => 0,
        'purchase_price' => 40,
        'selling_price' => 100,
    ]);

    return compact('user', 'etablissement', 'product', 'stock');
}

it('enregistre une commande livraison, son acompte choisi et notifie l’établissement', function () {
    Mail::fake();
    $context = makeDeliveryOrderTestContext();

    $this->post(route('livraisons.store'), [
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client livraison',
        'client_phone' => '0991111111',
        'delivery_address' => '12 avenue du Test, Gombe',
        'payment_plan' => 'two_installments',
        'deposit_amount' => 50,
        'product_ids' => [$context['product']->id],
        'quantities' => [$context['product']->id => 2],
    ])->assertRedirect(route('welcome'))->assertSessionHas('success');

    $order = DeliveryOrder::with('items')->firstOrFail();
    expect((float) $order->total_amount)->toBe(200.0);
    expect((float) $order->deposit_amount)->toBe(50.0);
    expect((float) $order->amount_paid)->toBe(0.0);
    expect($order->statut)->toBe('en_attente');
    expect($order->items->first()->product_name)->toBe('Produit livraison');
    expect($context['stock']->fresh()->quantity)->toBe(10);
    Mail::assertSent(DeliveryOrderReceived::class, fn ($mail) => $mail->hasTo($context['user']->email));
});

it('enregistre le paiement en une fois avec un montant dû égal au total', function () {
    $context = makeDeliveryOrderTestContext();

    $this->post(route('livraisons.store'), [
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client paiement comptant',
        'client_phone' => '0994444444',
        'delivery_address' => '24 avenue du Test, Gombe',
        'payment_plan' => 'one_time',
        'product_ids' => [$context['product']->id],
        'quantities' => [$context['product']->id => 2],
    ])->assertRedirect(route('welcome'));

    $order = DeliveryOrder::firstOrFail();
    expect($order->payment_plan)->toBe('one_time');
    expect((float) $order->total_amount)->toBe(200.0);
    expect((float) $order->deposit_amount)->toBe(200.0);
    expect((float) $order->amount_paid)->toBe(0.0);
    expect($order->statut)->toBe('en_attente');
});

it('refuse une commande qui dépasse le stock restant après les demandes en attente', function () {
    $context = makeDeliveryOrderTestContext(5);
    ProductReservationItem::create([
        'product_reservation_id' => \App\Models\ProductReservation::create([
            'etablissement_id' => $context['etablissement']->id,
            'client_name' => 'Autre client',
            'client_phone' => '0992222222',
            'statut' => 'en_attente',
        ])->id,
        'product_id' => $context['product']->id,
        'quantity' => 3,
    ]);
    $pendingDelivery = DeliveryOrder::create([
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client livraison en attente',
        'client_phone' => '0993333333',
        'delivery_address' => 'Adresse de test',
        'payment_plan' => 'one_time',
        'total_amount' => 100,
        'deposit_amount' => 100,
        'amount_paid' => 0,
        'statut' => 'en_attente',
    ]);
    $pendingDelivery->items()->create([
        'product_id' => $context['product']->id,
        'product_name' => $context['product']->name,
        'product_code' => $context['product']->code,
        'quantity' => 1,
        'unit_price' => 100,
        'line_total' => 100,
    ]);

    $this->from(route('welcome'))->post(route('livraisons.store'), [
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client livraison',
        'client_phone' => '0991111111',
        'delivery_address' => '12 avenue du Test, Gombe',
        'payment_plan' => 'one_time',
        'product_ids' => [$context['product']->id],
        'quantities' => [$context['product']->id => 3],
    ])->assertSessionHasErrors('quantities');

    expect(DeliveryOrder::count())->toBe(1);
    expect($context['stock']->fresh()->quantity)->toBe(5);
});

it('confirme acompte puis solde avant de livrer et de décrémenter le stock', function () {
    $context = makeDeliveryOrderTestContext(6);
    $order = DeliveryOrder::create([
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client livraison',
        'client_phone' => '0991111111',
        'delivery_address' => '12 avenue du Test, Gombe',
        'payment_plan' => 'two_installments',
        'total_amount' => 300,
        'deposit_amount' => 50,
        'amount_paid' => 0,
        'statut' => 'en_attente',
    ]);
    $order->items()->create([
        'product_id' => $context['product']->id,
        'product_name' => $context['product']->name,
        'product_code' => $context['product']->code,
        'quantity' => 3,
        'unit_price' => 100,
        'line_total' => 300,
    ]);

    $this->actingAs($context['user'])
        ->post(route('etablissements.livraisons.action', $order->id), ['action' => 'confirm_payment'])
        ->assertSessionHas('success');
    expect($order->fresh()->statut)->toBe('payee_partiellement');
    expect((float) $order->fresh()->amount_paid)->toBe(50.0);
    expect($context['stock']->fresh()->quantity)->toBe(6);

    $this->post(route('etablissements.livraisons.action', $order->id), ['action' => 'confirm_balance'])
        ->assertSessionHas('success');
    expect($order->fresh()->statut)->toBe('payee');

    $this->post(route('etablissements.livraisons.action', $order->id), ['action' => 'start_delivery'])
        ->assertSessionHas('success');
    expect($order->fresh()->statut)->toBe('en_livraison');
    expect($context['stock']->fresh()->quantity)->toBe(3);
    $this->assertDatabaseHas('movements', [
        'etablissement_id' => $context['etablissement']->id,
        'product_id' => $context['product']->id,
        'type' => 'out',
        'quantity' => 3,
        'before' => 6,
        'after' => 3,
    ]);

    $this->post(route('etablissements.livraisons.action', $order->id), ['action' => 'mark_delivered'])
        ->assertSessionHas('success');
    expect($order->fresh()->statut)->toBe('livree');
    expect($order->fresh()->delivered_at)->not->toBeNull();
});