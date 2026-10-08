<?php

use App\Models\Etablissement;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('rend la recherche par produit dans les formulaires entrée et sortie', function () {
    $user = User::factory()->gerant()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $category = Category::create([
        'nom' => 'Recherche',
        'description' => 'Produits de recherche',
        'status' => true,
    ]);
    $unit = Unit::create(['name' => 'Pièce recherche', 'symbol' => 'pc', 'status' => true]);
    $newProduct = Product::create([
        'etablissement_id' => $etablissement->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'NEW-001',
        'name' => 'Produit neuf',
        'status' => true,
    ]);
    $stockedProduct = Product::create([
        'etablissement_id' => $etablissement->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'STOCK-001',
        'name' => 'Produit stocké',
        'status' => true,
    ]);
    Stock::create([
        'etablissement_id' => $etablissement->id,
        'product_id' => $stockedProduct->id,
        'quantity' => 8,
        'purchase_price' => 10,
        'selling_price' => 20,
    ]);

    $this->actingAs($user)
        ->get(route('client.stocks.stock-in'))
        ->assertOk()
        ->assertSee('stockInProductSearch', false)
        ->assertSee('data-search="Produit neuf NEW-001"', false)
        ->assertSee('data-search="Produit stocké STOCK-001"', false)
        ->assertSee('toLocaleLowerCase', false);

    $this->get(route('client.stocks.stock-out'))
        ->assertOk()
        ->assertSee('stockOutProductSearch', false)
        ->assertSee('data-search="Produit stocké STOCK-001"', false)
        ->assertSee('toLocaleLowerCase', false);
});