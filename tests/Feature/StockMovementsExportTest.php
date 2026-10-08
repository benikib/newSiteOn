<?php

use App\Models\Category;
use App\Models\Etablissement;
use App\Models\Movement;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('exporte uniquement les mouvements qui correspondent aux filtres', function () {
    $user = User::factory()->gerant()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $category = Category::create(['nom' => 'Export', 'description' => 'Test export', 'status' => true]);
    $unit = Unit::create(['name' => 'Unité export', 'symbol' => 'u', 'status' => true]);
    $matchingProduct = Product::create([
        'etablissement_id' => $etablissement->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'MATCH-01',
        'name' => 'Produit filtré',
        'status' => true,
    ]);
    $otherProduct = Product::create([
        'etablissement_id' => $etablissement->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'OTHER-02',
        'name' => 'Produit exclu',
        'status' => true,
    ]);

    foreach ([$matchingProduct, $otherProduct] as $product) {
        Stock::create([
            'etablissement_id' => $etablissement->id,
            'product_id' => $product->id,
            'quantity' => 10,
            'purchase_price' => 5,
            'selling_price' => 10,
        ]);
    }

    Movement::create([
        'etablissement_id' => $etablissement->id,
        'product_id' => $matchingProduct->id,
        'type' => 'in',
        'quantity' => 4,
        'before' => 1,
        'after' => 5,
        'note' => 'Correspondance',
        'user_id' => $user->id,
        'created_at' => '2026-10-04 09:30:00',
    ]);
    Movement::create([
        'etablissement_id' => $etablissement->id,
        'product_id' => $otherProduct->id,
        'type' => 'out',
        'quantity' => 2,
        'before' => 10,
        'after' => 8,
        'note' => 'À exclure',
        'user_id' => $user->id,
        'created_at' => '2026-10-04 10:30:00',
    ]);

    $filters = [
        'etablissement_id' => $etablissement->id,
        'type' => 'in',
        'product_id' => $matchingProduct->id,
        'date_from' => '2026-10-01',
        'date_to' => '2026-10-10',
    ];

    $this->actingAs($user)
        ->get(route('client.stocks.movements.print', $filters))
        ->assertOk()
        ->assertSee('Établissement :')
        ->assertSee($etablissement->nom)
        ->assertSee('Du 01/10/2026 au 10/10/2026')
        ->assertSee('Produit filtré')
        ->assertDontSee('Produit exclu');

    $this->get(route('client.stocks.movements.pdf', $filters))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});