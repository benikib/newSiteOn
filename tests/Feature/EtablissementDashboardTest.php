<?php

use App\Models\Abonnement;
use App\Models\Category;
use App\Models\Etablissement;
use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('affiche les ventes du jour, le stock faible et les raccourcis principaux', function () {
    $this->travelTo(now()->parse('2026-10-08 12:00:00'));
    $user = User::factory()->gerant()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    Abonnement::create([
        'etablissement_id' => $etablissement->id,
        'date_debut' => '2026-10-01',
        'date_fin' => '2026-10-31',
        'statut' => 'actif',
        'type_operation' => 'creation',
        'motif' => 'Test dashboard',
        'created_by' => $user->id,
    ]);

    Order::create([
        'etablissement_id' => $etablissement->id,
        'user_id' => $user->id,
        'order_number' => 'POS-DASHBOARD-001',
        'customer_name' => 'Client test',
        'total_ht' => 1000,
        'total_tva' => 0,
        'total_amount' => 1000,
        'initial_amount' => 1000,
        'discount_amount' => 0,
        'payment_currency' => 'CDF',
        'payment_amount' => 1000,
        'status' => 'completed',
        'payment_method' => 'cash',
        'payment_status' => 'paid',
        'type' => 'pos',
        'order_date' => now(),
    ]);

    $category = Category::create(['nom' => 'Dashboard', 'description' => 'Test', 'status' => true]);
    $unit = Unit::create(['name' => 'Dashboard unit', 'symbol' => 'u', 'status' => true]);
    $product = Product::create([
        'etablissement_id' => $etablissement->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'DASH-001',
        'name' => 'Produit faible',
        'status' => true,
    ]);
    Stock::create([
        'etablissement_id' => $etablissement->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'minimum_stock' => 5,
        'purchase_price' => 10,
        'selling_price' => 20,
    ]);

    $this->actingAs($user)
        ->get(route('dashboard_ets'))
        ->assertOk()
        ->assertSee('Ventes du jour')
        ->assertSee('1 000,00')
        ->assertSee('Stock faible')
        ->assertSee('Point de vente')
        ->assertSee('Réservations');
});