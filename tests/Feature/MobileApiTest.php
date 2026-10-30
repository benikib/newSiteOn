<?php

use App\Models\Abonnement;
use App\Models\Category;
use App\Models\DeliveryOrder;
use App\Models\Etablissement;
use App\Models\Product;
use App\Models\Service;
use App\Models\Stock;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

function makeMobileApiContext(int $quantity = 8): array
{
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
        'date_debut' => today(),
        'date_fin' => today()->addDays(30),
        'statut' => 'actif',
        'type_operation' => 'creation',
        'motif' => 'Test API mobile',
        'created_by' => $user->id,
    ]);

    $category = Category::create([
        'nom' => 'API '.$etablissement->id,
        'description' => 'Catégorie de test mobile',
        'status' => true,
    ]);
    $unit = Unit::create([
        'name' => 'API '.$etablissement->id,
        'symbol' => 'u',
        'status' => true,
    ]);
    $product = Product::create([
        'etablissement_id' => $etablissement->id,
        'category_id' => $category->id,
        'unit_id' => $unit->id,
        'code' => 'API-'.$etablissement->id,
        'name' => 'Produit mobile',
        'status' => true,
    ]);
    $stock = Stock::create([
        'etablissement_id' => $etablissement->id,
        'product_id' => $product->id,
        'quantity' => $quantity,
        'minimum_stock' => 2,
        'purchase_price' => 10,
        'selling_price' => 20,
    ]);

    return compact('user', 'etablissement', 'product', 'stock');
}

it('authentifie un utilisateur mobile et permet de consulter son profil avec le jeton', function () {
    $user = User::factory()->gerant()->create([
        'email' => 'mobile@example.test',
        'password' => bcrypt('secret-password'),
    ]);

    $login = $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'secret-password',
        'device_name' => 'test-device',
    ])->assertOk()
        ->assertJsonPath('data.token_type', 'Bearer')
        ->assertJsonPath('data.user.id', $user->id);

    $this->withToken($login->json('data.token'))
        ->getJson('/api/v1/me')
        ->assertOk()
        ->assertJsonPath('data.user.email', $user->email);
});

it('protège les stocks par établissement et enregistre chaque mouvement transactionnellement', function () {
    $context = makeMobileApiContext();
    $token = $context['user']->createToken('test-device')->plainTextToken;
    $url = '/api/v1/establishments/'.$context['etablissement']->id;

    $this->withToken($token)
        ->postJson($url.'/movements', [
            'product_id' => $context['product']->id,
            'type' => 'in',
            'quantity' => 3,
            'purchase_price' => 12,
            'purchase_currency' => 'CDF',
            'selling_price' => 24,
            'selling_currency' => 'CDF',
        ])
        ->assertCreated()
        ->assertJsonPath('data.type', 'in');

    expect($context['stock']->fresh()->quantity)->toBe(11);
    $this->assertDatabaseHas('movements', [
        'etablissement_id' => $context['etablissement']->id,
        'product_id' => $context['product']->id,
        'type' => 'in',
        'before' => 8,
        'after' => 11,
    ]);

    $this->postJson($url.'/movements', [
        'product_id' => $context['product']->id,
        'type' => 'out',
        'quantity' => 12,
    ])->assertUnprocessable();

    expect($context['stock']->fresh()->quantity)->toBe(11);
});

it('refuse l’accès mobile à un établissement qui n’appartient pas à l’utilisateur', function () {
    $context = makeMobileApiContext();
    $other = Etablissement::factory()->create(['statut' => 'actif']);
    $token = $context['user']->createToken('test-device')->plainTextToken;

    $this->withToken($token)
        ->getJson('/api/v1/establishments/'.$other->id.'/stocks')
        ->assertNotFound();

    $this->patchJson('/api/v1/establishments/'.$other->id, [
        'latitude' => 4.05,
        'longitude' => 9.70,
    ])->assertNotFound();

    $this->postJson('/api/v1/establishments/'.$other->id.'/photos', [])
        ->assertNotFound();
});

it('met à jour les coordonnées avec les noms longs ou courts et les expose dans l’API publique', function () {
    $context = makeMobileApiContext();
    $token = $context['user']->createToken('test-device')->plainTextToken;
    $url = '/api/v1/establishments/'.$context['etablissement']->id;

    $this->withToken($token)
        ->patchJson($url, ['latitude' => 4.05, 'longitude' => 9.70])
        ->assertOk()
        ->assertJsonPath('data.latitude', 4.05)
        ->assertJsonPath('data.longitude', 9.7);

    expect((float) $context['etablissement']->fresh()->latitude)->toBe(4.05)
        ->and((float) $context['etablissement']->fresh()->longitude)->toBe(9.7);

    $this->patchJson($url, ['lat' => -12.3, 'lng' => 45.6])
        ->assertOk()
        ->assertJsonPath('data.latitude', -12.3)
        ->assertJsonPath('data.longitude', 45.6);

    $this->getJson('/api/v1/public/etablissements/'.$context['etablissement']->id)
        ->assertOk()
        ->assertJsonPath('data.address.latitude', -12.3)
        ->assertJsonPath('data.address.longitude', 45.6);
});

it('rejette les coordonnées mélangées ou hors limites', function () {
    $context = makeMobileApiContext();
    $token = $context['user']->createToken('test-device')->plainTextToken;

    $this->withToken($token)
        ->patchJson('/api/v1/establishments/'.$context['etablissement']->id, [
            'lat' => 4.05,
            'longitude' => 9.70,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('coordinates');

    $this->patchJson('/api/v1/establishments/'.$context['etablissement']->id, [
        'latitude' => 91,
        'longitude' => 9.70,
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('latitude');
});

it('gère les images d’établissement, de produit et la galerie via des APIs réservées au gestionnaire', function () {
    Storage::fake('public');
    $context = makeMobileApiContext();
    $token = $context['user']->createToken('test-device')->plainTextToken;
    $baseUrl = '/api/v1/establishments/'.$context['etablissement']->id;

    $this->withToken($token)
        ->post($baseUrl.'/photo', ['photo' => UploadedFile::fake()->image('cover.jpg')], [
            'Accept' => 'application/json',
        ])
        ->assertOk()
        ->assertJsonPath('data.id', $context['etablissement']->id);
    expect($context['etablissement']->fresh()->image_path)->not->toBeNull();

    $galleryResponse = $this->post($baseUrl.'/photos', [
        'photos' => [
            UploadedFile::fake()->image('gallery-one.jpg'),
            UploadedFile::fake()->image('gallery-two.jpg'),
        ],
        'titre' => 'Galerie',
        'alt_text' => 'Photo de galerie',
    ], ['Accept' => 'application/json'])
        ->assertCreated()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.title', 'Galerie');
    $this->getJson($baseUrl.'/photos')->assertOk()->assertJsonCount(2, 'data');

    $secondPhotoId = $galleryResponse->json('data.1.id');
    $this->patchJson($baseUrl.'/photos/'.$secondPhotoId, ['est_publique' => false])
        ->assertOk()
        ->assertJsonPath('data.id', $secondPhotoId);

    $this->getJson('/api/v1/public/etablissements/'.$context['etablissement']->id.'/photos')
        ->assertOk()
        ->assertJsonCount(1, 'data');
    $this->getJson($baseUrl.'/photos')->assertOk()->assertJsonCount(2, 'data');
    $this->getJson('/api/v1/public/etablissements/'.$context['etablissement']->id)
        ->assertOk()
        ->assertJsonPath('data.image_url', asset('storage/'.$context['etablissement']->fresh()->image_path))
        ->assertJsonCount(1, 'data.photos');

    $this->deleteJson($baseUrl.'/photos/'.$secondPhotoId)
        ->assertOk()
        ->assertJsonPath('data.deleted', true);

    $this->post($baseUrl.'/products/'.$context['product']->id.'/image', [
        'image' => UploadedFile::fake()->image('product.jpg'),
    ], ['Accept' => 'application/json'])
        ->assertOk()
        ->assertJsonPath('data.id', $context['product']->id);
    expect($context['product']->fresh()->image)->not->toBeNull();

    $this->getJson('/api/v1/public/etablissements/'.$context['etablissement']->id.'/products')
        ->assertOk()
        ->assertJsonPath('data.0.image_url', asset('storage/'.$context['product']->fresh()->image));

    $this->deleteJson($baseUrl.'/photo')
        ->assertOk()
        ->assertJsonPath('data.image_url', null);

    $this->deleteJson($baseUrl.'/products/'.$context['product']->id.'/image')
        ->assertOk()
        ->assertJsonPath('data.image_url', null);
});

it('retourne la disponibilité publique après prise en compte des réservations et livraisons en attente', function () {
    $context = makeMobileApiContext();

    $this->postJson('/api/v1/public/product-reservations', [
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client mobile',
        'client_phone' => '0990000000',
        'items' => [[
            'product_id' => $context['product']->id,
            'quantity' => 3,
        ]],
    ])->assertCreated()
        ->assertJsonPath('data.status', 'en_attente');

    $delivery = DeliveryOrder::create([
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client livraison',
        'client_phone' => '0990000001',
        'delivery_address' => 'Adresse de test',
        'payment_plan' => 'one_time',
        'total_amount' => 40,
        'deposit_amount' => 40,
        'amount_paid' => 0,
        'statut' => 'en_attente',
    ]);
    $delivery->items()->create([
        'product_id' => $context['product']->id,
        'product_name' => $context['product']->name,
        'product_code' => $context['product']->code,
        'quantity' => 2,
        'unit_price' => 20,
        'line_total' => 40,
    ]);

    $this->getJson('/api/v1/public/etablissements/'.$context['etablissement']->id.'/products')
        ->assertOk()
        ->assertJsonPath('data.0.stock.available_quantity', 3);
    expect($context['stock']->fresh()->quantity)->toBe(8);
});

it('crée une réservation de service via une réponse JSON versionnée', function () {
    $context = makeMobileApiContext();
    $service = Service::create([
        'nom' => 'Consultation mobile',
        'description' => 'Service de test',
        'prix' => 25,
        'disponibilite' => true,
        'etablissement_id' => $context['etablissement']->id,
    ]);

    $this->postJson('/api/v1/public/reservations', [
        'service_id' => $service->id,
        'date' => today()->addDay()->toDateString(),
        'client_name' => 'Client mobile',
        'client_phone' => '0990000000',
    ])->assertCreated()
        ->assertJsonPath('data.status', 'en_attente')
        ->assertJsonPath('data.id', 1);
});

it('enregistre une vente POS depuis l’API et retourne un résultat mobile normalisé', function () {
    $context = makeMobileApiContext();
    $token = $context['user']->createToken('test-device')->plainTextToken;

    $this->withToken($token)
        ->postJson('/api/v1/establishments/'.$context['etablissement']->id.'/sales', [
            'items' => [[
                'product_id' => $context['product']->id,
                'quantity' => 2,
                'tva_rate' => 0,
            ]],
            'payment_method' => 'cash',
            'payment_currency' => 'CDF',
        ])
        ->assertOk()
        ->assertJsonPath('data.total_ttc', 40)
        ->assertJsonStructure(['data' => ['id', 'order_number', 'payment_amount'], 'message']);

    expect($context['stock']->fresh()->quantity)->toBe(6);
});

it('crée une demande de livraison avec le format items de l’API mobile', function () {
    $context = makeMobileApiContext();

    $this->postJson('/api/v1/public/delivery-orders', [
        'etablissement_id' => $context['etablissement']->id,
        'client_name' => 'Client mobile',
        'client_phone' => '0990000000',
        'delivery_address' => 'Adresse mobile',
        'payment_plan' => 'two_installments',
        'deposit_amount' => 10,
        'items' => [[
            'product_id' => $context['product']->id,
            'quantity' => 2,
        ]],
    ])->assertCreated()
        ->assertJsonPath('data.total_amount', '40.00')
        ->assertJsonPath('data.status', 'en_attente');

    expect($context['stock']->fresh()->quantity)->toBe(8);
});
