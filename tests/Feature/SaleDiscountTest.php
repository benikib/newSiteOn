<?php

use App\Models\Category;
use App\Models\Etablissement;
use App\Models\Product;
use App\Models\Stock;
use App\Models\TauxDeChange;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function createSaleDiscountTestContext(): array
{
    $user = User::factory()->gerant()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $category = Category::create([
        'nom' => 'Test',
        'description' => 'Catégorie test',
        'status' => true,
    ]);
    $unit = Unit::create([
        'name' => 'Unité test',
        'symbol' => 'u',
        'status' => true,
    ]);
    $product = Product::create([
        'etablissement_id' => $etablissement->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'TEST-001',
        'name' => 'Produit test',
        'status' => true,
    ]);
    $stock = Stock::create([
        'etablissement_id' => $etablissement->id,
        'product_id' => $product->id,
        'quantity' => 5,
        'purchase_price' => 50,
        'selling_price' => 100,
    ]);

    return compact('user', 'etablissement', 'product', 'stock');
}

it('applique un pourcentage à la facture sans modifier le prix stock', function () {
    $context = createSaleDiscountTestContext();
    $user = $context['user'];
    $etablissement = $context['etablissement'];
    $product = $context['product'];
    $stock = $context['stock'];

    $this->actingAs($user)->postJson(route('client.sales.store'), [
        'etablissement_id' => $etablissement->id,
        'payment_method' => 'cash',
        'payment_currency' => 'CDF',
        'discount_type' => 'percentage',
        'discount_value' => 10,
        'items' => [[
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 1,
            'tva_rate' => 0,
        ]],
    ])->assertOk()->assertJsonPath('initial_total', 100)->assertJsonPath('discount_amount', 10)->assertJsonPath('total_ttc', 90);

    $order = \App\Models\Order::firstOrFail();
    expect((float) $order->items->first()->price_ht)->toBe(100.0);
    expect((float) $order->initial_amount)->toBe(100.0);
    expect((float) $order->discount_amount)->toBe(10.0);
    expect((float) $order->total_amount)->toBe(90.0);
    expect((float) $stock->fresh()->selling_price)->toBe(100.0);

    $this->get(route('client.sales.invoice', $order->id))
        ->assertOk()
        ->assertSee('Montant initial TTC')
        ->assertSee('10,00 CDF')
        ->assertSee('Total net TTC')
        ->assertSee('90,00 CDF');
});

it('applique un montant de réduction en CDF', function () {
    $context = createSaleDiscountTestContext();
    $user = $context['user'];
    $etablissement = $context['etablissement'];
    $product = $context['product'];
    $stock = $context['stock'];

    $this->actingAs($user)->postJson(route('client.sales.store'), [
        'etablissement_id' => $etablissement->id,
        'payment_method' => 'cash',
        'payment_currency' => 'CDF',
        'discount_type' => 'amount',
        'discount_value' => 15,
        'items' => [[
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 1,
            'tva_rate' => 0,
        ]],
    ])->assertOk()->assertJsonPath('discount_amount', 15)->assertJsonPath('total_ttc', 85);

    expect((float) \App\Models\Order::firstOrFail()->total_amount)->toBe(85.0);
    expect((float) $stock->fresh()->selling_price)->toBe(100.0);
});

it('convertit une réduction monétaire USD en CDF avant le calcul du net', function () {
    $context = createSaleDiscountTestContext();
    $user = $context['user'];
    $etablissement = $context['etablissement'];
    $product = $context['product'];
    $stock = $context['stock'];

    TauxDeChange::create(['usd_cdf' => 2500, 'date' => '2026-10-08']);
    $stock->update(['selling_price' => 10000]);

    $this->actingAs($user)->postJson(route('client.sales.store'), [
        'etablissement_id' => $etablissement->id,
        'payment_method' => 'cash',
        'payment_currency' => 'USD',
        'discount_type' => 'amount',
        'discount_value' => 1,
        'items' => [[
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 1,
            'tva_rate' => 0,
        ]],
    ])->assertOk()->assertJsonPath('initial_total', 10000)->assertJsonPath('discount_amount', 2500)->assertJsonPath('total_ttc', 7500);

    $order = \App\Models\Order::firstOrFail();
    expect((float) $order->payment_amount)->toBe(3.0);
    expect((float) $stock->fresh()->selling_price)->toBe(10000.0);
});