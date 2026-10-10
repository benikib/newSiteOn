<?php

use App\Models\Etablissement;
use App\Models\TypeEtablissement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

it('convertit un numéro local et enregistre le message WhatsApp pour le bon établissement', function () {
    $user = User::factory()->gerant()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($user)
        ->put(route('etablissements.updateContact', $etablissement), [
            'whatsapp_number' => '0812 345 678',
            'whatsapp_message' => 'Bonjour, contactez-nous.',
        ])
        ->assertRedirect();

    expect($etablissement->fresh()->whatsapp_number)->toBe('243812345678');
    expect($etablissement->fresh()->whatsapp_message)->toBe('Bonjour, contactez-nous.');

    $this->from('/parametres')
        ->actingAs($user)
        ->put(route('etablissements.updateContact', $etablissement), [
            'whatsapp_number' => '243ABC123',
        ])
        ->assertSessionHasErrors('whatsapp_number');
});

it('refuse à un établissement de modifier le numéro WhatsApp d’un autre', function () {
    $user = User::factory()->gerant()->create();
    $assigned = Etablissement::factory()->create(['statut' => 'actif']);
    $other = Etablissement::factory()->create(['whatsapp_number' => '243899999999']);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $assigned->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($user)
        ->put(route('etablissements.updateContact', $other), ['whatsapp_number' => '243812345678'])
        ->assertForbidden();

    expect($other->fresh()->whatsapp_number)->toBe('243899999999');
});

it('affiche le chat uniquement avec le numéro configuré pour la page demandée', function () {
    $type = TypeEtablissement::create([
        'nom' => 'Type WhatsApp test',
        'description' => 'Type de test WhatsApp',
    ]);
    $etablissement = Etablissement::factory()->create([
        'type_etablissement_id' => $type->id,
        'whatsapp_number' => '243812345678',
        'whatsapp_message' => 'Bonjour boutique, des informations svp.',
    ]);
    $other = Etablissement::factory()->create([
        'type_etablissement_id' => $type->id,
        'whatsapp_number' => '243899999999',
    ]);

    $this->get(route('ets.info', $etablissement))
        ->assertOk()
        ->assertSee('id="shop-whatsapp-fab"', false)
        ->assertSee('wa.me/243812345678', false)
        ->assertSee('target="_blank" rel="noopener"', false)
        ->assertSee(rawurlencode('Bonjour boutique, des informations svp.'), false)
        ->assertDontSee('wa.me/243899999999', false);

    $this->get(route('ets.info', $other))
        ->assertOk()
        ->assertSee('wa.me/243899999999', false)
        ->assertDontSee('wa.me/243812345678', false);

    $withoutNumber = Etablissement::factory()->create([
        'type_etablissement_id' => $type->id,
        'whatsapp_number' => null,
    ]);
    $this->get(route('ets.info', $withoutNumber))
        ->assertOk()
        ->assertDontSee('id="shop-whatsapp-fab"', false);
});