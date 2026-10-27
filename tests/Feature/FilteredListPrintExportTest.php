<?php

use App\Models\Category;
use App\Models\Etablissement;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function createFilteredListExportContext(): array
{
    $user = User::factory()->gerant()->create();
    $etablissement = Etablissement::factory()->create([
        'nom' => 'Établissement export',
        'statut' => 'actif',
    ]);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $category = Category::create([
        'nom' => 'Catégorie export A',
        'description' => 'Catégorie pour exporter les listes A',
        'status' => true,
    ]);
    $otherCategory = Category::create([
        'nom' => 'Catégorie export B',
        'description' => 'Catégorie pour exporter les listes B',
        'status' => true,
    ]);
    $unit = Unit::create(['name' => 'Unité export', 'symbol' => 'u', 'status' => true]);

    return compact('user', 'etablissement', 'category', 'otherCategory', 'unit');
}

it('imprime et exporte en PDF uniquement les produits correspondant aux filtres', function () {
    $context = createFilteredListExportContext();
    $filteredProduct = Product::create([
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['category']->id,
        'unit_id' => $context['unit']->id,
        'code' => 'EXPORT-A',
        'name' => 'Produit filtré export',
        'status' => true,
    ]);
    Product::create([
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['otherCategory']->id,
        'unit_id' => $context['unit']->id,
        'code' => 'EXPORT-B',
        'name' => 'Produit exclu export',
        'status' => true,
    ]);
    Stock::create([
        'etablissement_id' => $context['etablissement']->id,
        'product_id' => $filteredProduct->id,
        'quantity' => 6,
        'purchase_price' => 20,
        'selling_price' => 30,
    ]);

    $query = [
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['category']->id,
    ];

    $this->actingAs($context['user'])
        ->get(route('client.products.index', $query))
        ->assertOk()
        ->assertSee('Imprimer')
        ->assertSee('Télécharger en PDF');

    $this->get(route('client.products.print', $query))
        ->assertOk()
        ->assertSee('Établissement export')
        ->assertSee('Produit filtré export')
        ->assertDontSee('Produit exclu export')
        ->assertSee(now()->format('d/m/Y'));

    $this->get(route('client.products.pdf', $query))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('imprime et exporte en PDF uniquement les stocks correspondant aux filtres', function () {
    $context = createFilteredListExportContext();
    $filteredProduct = Product::create([
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['category']->id,
        'unit_id' => $context['unit']->id,
        'code' => 'STOCK-EXPORT-A',
        'name' => 'Stock filtré export',
        'status' => true,
    ]);
    $excludedProduct = Product::create([
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['otherCategory']->id,
        'unit_id' => $context['unit']->id,
        'code' => 'STOCK-EXPORT-B',
        'name' => 'Stock exclu export',
        'status' => true,
    ]);
    foreach ([$filteredProduct, $excludedProduct] as $product) {
        Stock::create([
            'etablissement_id' => $context['etablissement']->id,
            'product_id' => $product->id,
            'quantity' => 6,
            'purchase_price' => 20,
            'selling_price' => 30,
        ]);
    }

    $query = [
        'etablissement_id' => $context['etablissement']->id,
        'category_id' => $context['category']->id,
    ];

    $this->actingAs($context['user'])
        ->get(route('client.stocks.index', $query))
        ->assertOk()
        ->assertSee('Imprimer')
        ->assertSee('Télécharger en PDF');

    $this->get(route('client.stocks.print', $query))
        ->assertOk()
        ->assertSee('Établissement export')
        ->assertSee('Stock filtré export')
        ->assertDontSee('Stock exclu export')
        ->assertSee(now()->format('d/m/Y'));

    $this->get(route('client.stocks.pdf', $query))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
