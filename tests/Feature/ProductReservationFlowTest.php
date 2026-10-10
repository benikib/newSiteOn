<?php

use App\Models\Abonnement;
use App\Models\Category;
use App\Models\Etablissement;
use App\Models\DeliveryOrder;
use App\Models\Movement;
use App\Models\Product;
use App\Models\ProductReservation;
use App\Models\ProductReservationItem;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function makeProductReservationContext(int $quantity = 5): array
{
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    $category = Category::create(['nom' => 'Réservation', 'description' => 'Test', 'status' => true]);
    $unit = Unit::create(['name' => 'Unité réservation', 'symbol' => 'u', 'status' => true]);
    $product = Product::create([
        'etablissement_id' => $etablissement->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'RES-' . $etablissement->id,
        'name' => 'Article réservable',
        'status' => true,
    ]);
    $stock = Stock::create([
        'etablissement_id' => $etablissement->id,
        'product_id' => $product->id,
        'quantity' => $quantity,
        'minimum_stock' => 0,
        'purchase_price' => 10,
        'selling_price' => 20,
    ]);

    return compact('etablissement', 'product', 'stock');
}

it('crée une demande publique et refuse une quantité qui dépasse la disponibilité restante', function () {
    $context = makeProductReservationContext(5);
    $payload = [
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client test',
        'client_phone' => '0990000000',
        'message' => 'Merci de me rappeler.',
        'product_ids' => [$context['product']->id],
        'quantities' => [$context['product']->id => 3],
    ];

    $this->post(route('reservations.articles.store'), $payload)
        ->assertRedirect(route('ets.info', $context['etablissement']->id))
        ->assertSessionHas('success');

    $reservation = ProductReservation::with('items')->firstOrFail();
    expect($reservation->statut)->toBe('en_attente');
    expect($reservation->message)->toBe('Merci de me rappeler.');
    expect($reservation->items->first()->quantity)->toBe(3);
    expect($context['stock']->fresh()->quantity)->toBe(5);

    $payload['quantities'][$context['product']->id] = 3;
    $this->from(route('welcome'))->post(route('reservations.articles.store'), $payload)
        ->assertSessionHasErrors('quantities');
    expect(ProductReservation::count())->toBe(1);
});

it('confirme une réservation uniquement pour son établissement et trace la sortie de stock', function () {
    $context = makeProductReservationContext(5);
    $user = User::factory()->gerant()->create();
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $context['etablissement']->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Abonnement::create([
        'etablissement_id' => $context['etablissement']->id,
        'date_debut' => today(),
        'date_fin' => today()->addDays(20),
        'statut' => 'actif',
        'type_operation' => 'creation',
        'motif' => 'Test de réservation',
        'created_by' => $user->id,
    ]);
    $reservation = ProductReservation::create([
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client test',
        'client_phone' => '0990000000',
        'statut' => 'en_attente',
    ]);
    ProductReservationItem::create([
        'product_reservation_id' => $reservation->id,
        'product_id' => $context['product']->id,
        'quantity' => 2,
    ]);

    $this->actingAs($user)
        ->get(route('etablissements.reservation.index'))
        ->assertOk()
        ->assertSee('Demandes de réservation d’articles')
        ->assertSee('Article réservable')
        ->assertSee('0990000000');

    $this->post(route('etablissements.reservation.articles.statut', $reservation->id), ['statut' => 'confirmé'])
        ->assertSessionHas('success');

    expect($reservation->fresh()->statut)->toBe('confirmé');
    expect($context['stock']->fresh()->quantity)->toBe(3);
    $this->assertDatabaseHas('movements', [
        'etablissement_id' => $context['etablissement']->id,
        'product_id' => $context['product']->id,
        'type' => 'out',
        'quantity' => 2,
        'before' => 5,
        'after' => 3,
    ]);
});

it('refuse une demande sans réduire le stock', function () {
    $context = makeProductReservationContext(4);
    $user = User::factory()->gerant()->create();
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $context['etablissement']->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Abonnement::create([
        'etablissement_id' => $context['etablissement']->id,
        'date_debut' => today(),
        'date_fin' => today()->addDays(20),
        'statut' => 'actif',
        'type_operation' => 'creation',
        'motif' => 'Test de réservation',
        'created_by' => $user->id,
    ]);
    $reservation = ProductReservation::create([
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client test',
        'client_phone' => '0990000000',
        'statut' => 'en_attente',
    ]);
    ProductReservationItem::create([
        'product_reservation_id' => $reservation->id,
        'product_id' => $context['product']->id,
        'quantity' => 2,
    ]);

    $this->actingAs($user)
        ->post(route('etablissements.reservation.articles.statut', $reservation->id), ['statut' => 'rejeté'])
        ->assertSessionHas('success');

    expect($reservation->fresh()->statut)->toBe('rejeté');
    expect($context['stock']->fresh()->quantity)->toBe(4);
    expect(Movement::count())->toBe(0);
});

it('affiche le vrai catalogue sur sa page dédiée et recherche sans tenir compte des accents', function () {
    $context = makeProductReservationContext(5);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('Article réservable')
        ->assertSee('5 disponibles');

    $this->get(route('products.index', ['q' => 'réservable']))
        ->assertOk()
        ->assertSee('Article réservable')
        ->assertDontSee('Le catalogue se prépare');
});

it('publie le catalogue avec la disponibilité réelle après réservations en attente', function () {
    $context = makeProductReservationContext(5);
    ProductReservation::create([
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client en attente',
        'client_phone' => '0990000001',
        'statut' => 'en_attente',
    ])->items()->create([
        'product_id' => $context['product']->id,
        'quantity' => 2,
    ]);

    $this->get(route('ets.info', $context['etablissement']->id))
        ->assertOk()
        ->assertSee('Le catalogue')
        ->assertSee('Article réservable')
        ->assertSee('data-available="3"', false);
});

it('ne permet pas de réserver le stock engagé dans une commande de livraison', function () {
    $context = makeProductReservationContext(5);
    $deliveryOrder = DeliveryOrder::create([
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client livraison',
        'client_phone' => '0990000002',
        'delivery_address' => 'Adresse test',
        'payment_plan' => 'one_time',
        'total_amount' => 40,
        'deposit_amount' => 0,
        'amount_paid' => 0,
        'statut' => 'en_attente',
    ]);
    $deliveryOrder->items()->create([
        'product_id' => $context['product']->id,
        'product_name' => $context['product']->name,
        'product_code' => $context['product']->code,
        'quantity' => 2,
        'unit_price' => 20,
        'line_total' => 40,
    ]);

    $this->from(route('ets.info', $context['etablissement']->id))
        ->post(route('reservations.articles.store'), [
            'etablissement_id' => $context['etablissement']->id,
            'client_name' => 'Client test',
            'client_phone' => '0990000000',
            'product_ids' => [$context['product']->id],
            'quantities' => [$context['product']->id => 4],
        ])
        ->assertSessionHasErrors('quantities');

    expect(ProductReservation::count())->toBe(0);
});