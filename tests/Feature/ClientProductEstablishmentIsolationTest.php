<?php

use App\Models\Category;
use App\Models\Etablissement;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function createClientProductIsolationContext(): array
{
    $user = User::factory()->gerant()->create();
    $etablissementA = Etablissement::factory()->create(['statut' => 'actif']);
    $etablissementB = Etablissement::factory()->create(['statut' => 'actif']);

    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissementA->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $category = Category::create([
        'nom' => 'Isolation',
        'description' => 'Catégorie de test',
        'status' => true,
    ]);
    $unit = Unit::create([
        'name' => 'Unité isolation',
        'symbol' => 'u',
        'status' => true,
    ]);

    $productA = Product::create([
        'etablissement_id' => $etablissementA->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'ISOL-A',
        'name' => 'Produit commun A',
        'status' => true,
    ]);
    $productB = Product::create([
        'etablissement_id' => $etablissementB->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'ISOL-B',
        'name' => 'Produit commun B',
        'status' => true,
    ]);
    Stock::create([
        'etablissement_id' => $etablissementA->id,
        'product_id' => $productA->id,
        'quantity' => 3,
        'purchase_price' => 10,
        'selling_price' => 20,
    ]);

    return compact('user', 'etablissementA', 'etablissementB', 'productA', 'productB');
}

it('limite la liste et la recherche POS aux produits de l’établissement autorisé', function () {
    $context = createClientProductIsolationContext();

    $this->actingAs($context['user'])
        ->get(route('client.products.index', [
            'etablissement_id' => $context['etablissementB']->id,
            'search' => 'Produit commun',
        ]))
        ->assertOk()
        ->assertSee('Produit commun A')
        ->assertDontSee('Produit commun B');

    $this->get(route('client.sales.search', [
        'etablissement_id' => $context['etablissementB']->id,
        'search' => 'Produit commun',
    ]))
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonFragment(['name' => 'Produit commun A'])
        ->assertJsonMissing(['name' => 'Produit commun B']);
});

it('n’affiche pas et refuse l’entrée en stock d’un produit d’un autre établissement', function () {
    $context = createClientProductIsolationContext();

    $this->actingAs($context['user'])
        ->get(route('stock.products.index'))
        ->assertRedirect();

    $this->get(route('admin.stocks.index'))
        ->assertRedirect();

    $this->get(route('client.stocks.stock-in', [
        'etablissement_id' => $context['etablissementA']->id,
    ]))
        ->assertOk()
        ->assertDontSee('ISOL-B');

    $this->post(route('client.stocks.stock-in'), [
        'etablissement_id' => $context['etablissementA']->id,
        'product_id' => $context['productB']->id,
        'quantity' => 1,
        'purchase_price' => 10,
        'purchase_currency' => 'CDF',
    ])->assertSessionHasErrors('items.0.product_id');

    expect(Stock::where('product_id', $context['productB']->id)->exists())->toBeFalse();
});
