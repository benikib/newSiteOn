<?php

use App\Models\Category;
use App\Models\Etablissement;
use App\Models\Product;
use App\Models\Stock;
use App\Models\TauxDeChange;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function createProductDefaultPriceContext(): array
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
        'nom' => 'Prix produit',
        'description' => 'Catégorie de test',
        'status' => true,
    ]);
    $unit = Unit::create([
        'name' => 'Unité prix',
        'symbol' => 'u',
        'status' => true,
    ]);

    return compact('user', 'etablissement', 'category', 'unit');
}

it('enregistre et affiche le prix par défaut du produit avec sa devise', function () {
    $context = createProductDefaultPriceContext();
    TauxDeChange::create(['usd_cdf' => 2500, 'date' => '2026-10-08']);

    $this->actingAs($context['user'])
        ->get(route('client.products.create'))
        ->assertOk()
        ->assertSee('name="selling_price"', false)
        ->assertSee('name="selling_currency"', false);

    $this->post(route('client.products.store'), [
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['category']->id,
        'unit_id' => $context['unit']->id,
        'code' => 'PRICE-001',
        'name' => 'Produit prix',
        'selling_price' => 2,
        'selling_currency' => 'USD',
        'status' => 1,
    ])
        ->assertRedirect(route('client.products.index', ['etablissement_id' => $context['etablissement']->id]));

    $product = Product::where('code', 'PRICE-001')->firstOrFail();
    $stock = $product->stock;
    expect((float) $stock->selling_price)->toBe(5000.0);
    expect((float) $stock->selling_price_original)->toBe(2.0);
    expect($stock->selling_currency)->toBe('USD');

    $this->get(route('client.products.index', ['etablissement_id' => $context['etablissement']->id]))
        ->assertOk()
        ->assertSee('Prix de vente')
        ->assertSee('2,00')
        ->assertSee('USD');
});

it('utilise le prix par défaut du produit lors d’une entrée de stock sans nouveau prix de vente', function () {
    $context = createProductDefaultPriceContext();
    $product = Product::create([
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['category']->id,
        'unit_id' => $context['unit']->id,
        'code' => 'PRICE-002',
        'name' => 'Produit stock',
        'status' => true,
    ]);
    $stock = Stock::create([
        'etablissement_id' => $context['etablissement']->id,
        'product_id' => $product->id,
        'quantity' => 0,
        'purchase_price' => 0,
        'selling_price' => 125,
        'selling_price_original' => 125,
        'selling_currency' => 'CDF',
    ]);

    $this->actingAs($context['user'])
        ->post(route('client.stocks.stock-in'), [
            'etablissement_id' => $context['etablissement']->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'purchase_price' => 50,
            'purchase_currency' => 'CDF',
        ])
        ->assertRedirect(route('client.stocks.stock-in', ['etablissement_id' => $context['etablissement']->id]));

    expect($stock->fresh()->quantity)->toBe(3);
    expect((float) $stock->fresh()->selling_price)->toBe(125.0);
    expect((float) $stock->fresh()->selling_price_original)->toBe(125.0);
    expect($stock->fresh()->selling_currency)->toBe('CDF');

    $this->get(route('client.stocks.stock-in'))
        ->assertOk()
        ->assertSee('data-selling-price="125.00"', false)
        ->assertSee('data-selling-currency="CDF"', false);

    $this->post(route('client.stocks.stock-in'), [
        'etablissement_id' => $context['etablissement']->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'purchase_price' => 50,
        'purchase_currency' => 'CDF',
        'selling_price' => 140,
        'selling_currency' => 'CDF',
    ])->assertRedirect(route('client.stocks.stock-in', ['etablissement_id' => $context['etablissement']->id]));

    expect((float) $stock->fresh()->selling_price)->toBe(140.0);
});

it('enregistre plusieurs produits en une seule entrée sans quitter le formulaire', function () {
    $context = createProductDefaultPriceContext();
    $productA = Product::create([
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['category']->id,
        'unit_id' => $context['unit']->id,
        'code' => 'MULTI-001',
        'name' => 'Produit multiple un',
        'status' => true,
    ]);
    $productB = Product::create([
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['category']->id,
        'unit_id' => $context['unit']->id,
        'code' => 'MULTI-002',
        'name' => 'Produit multiple deux',
        'status' => true,
    ]);

    $this->actingAs($context['user'])
        ->get(route('client.stocks.stock-in'))
        ->assertOk()
        ->assertSee('Ajouter un autre produit')
        ->assertSee('id="stockInForm"', false);

    $this->postJson(route('client.stocks.stock-in'), [
        'etablissement_id' => $context['etablissement']->id,
        'items' => [
            [
                'product_id' => $productA->id,
                'quantity' => 2,
                'purchase_price' => 10,
                'purchase_currency' => 'CDF',
            ],
            [
                'product_id' => $productB->id,
                'quantity' => 5,
                'purchase_price' => 15,
                'purchase_currency' => 'CDF',
                'selling_price' => 30,
                'selling_currency' => 'CDF',
            ],
        ],
        'note' => 'Approvisionnement groupé',
    ])->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('message', 'Les stocks ont été enregistrés avec succès !');

    expect(Stock::where('product_id', $productA->id)->value('quantity'))->toBe(2);
    expect(Stock::where('product_id', $productB->id)->value('quantity'))->toBe(5);
    expect(DB::table('movements')->whereIn('product_id', [$productA->id, $productB->id])->count())->toBe(2);
});

it('met à jour le prix par défaut lors de la modification du produit', function () {
    $context = createProductDefaultPriceContext();
    $product = Product::create([
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['category']->id,
        'unit_id' => $context['unit']->id,
        'code' => 'PRICE-003',
        'name' => 'Produit à modifier',
        'status' => true,
    ]);
    $stock = Stock::create([
        'etablissement_id' => $context['etablissement']->id,
        'product_id' => $product->id,
        'quantity' => 4,
        'purchase_price' => 50,
        'selling_price' => 100,
        'selling_price_original' => 100,
        'selling_currency' => 'CDF',
    ]);

    $this->actingAs($context['user'])
        ->get(route('client.products.edit', $product->id))
        ->assertOk()
        ->assertSee('value="100.00"', false)
        ->assertSee('name="selling_currency"', false);

    $this->put(route('client.products.update', $product->id), [
        'category_id' => $context['category']->id,
        'unit_id' => $context['unit']->id,
        'code' => $product->code,
        'name' => 'Produit modifié',
        'selling_price' => 150,
        'selling_currency' => 'CDF',
        'status' => 1,
    ])
        ->assertRedirect(route('client.products.index', ['etablissement_id' => $context['etablissement']->id]));

    expect((float) $stock->fresh()->selling_price)->toBe(150.0);
    expect((float) $stock->fresh()->selling_price_original)->toBe(150.0);
    expect($stock->fresh()->quantity)->toBe(4);
});
