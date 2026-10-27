<?php

use App\Models\Category;
use App\Models\Etablissement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function createClientCategoryContext(): array
{
    $user = User::factory()->gerant()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return compact('user', 'etablissement');
}

it('affiche les catégories client avec les colonnes du schéma', function () {
    $context = createClientCategoryContext();
    Category::create([
        'nom' => 'Catégorie active client',
        'description' => 'Description catégorie',
        'status' => true,
    ]);
    Category::create([
        'nom' => 'Catégorie inactive client',
        'description' => 'Description inactive',
        'status' => false,
    ]);

    $this->actingAs($context['user'])
        ->get(route('client.categories.index'))
        ->assertOk()
        ->assertSee('Catégorie active client')
        ->assertSee('Catégorie inactive client')
        ->assertSee('Actif')
        ->assertSee('Inactif');

    $this->get(route('client.categories.index', ['statut' => 'actif']))
        ->assertOk()
        ->assertSee('Catégorie active client')
        ->assertDontSee('Catégorie inactive client');
});

it('crée et change le statut des catégories client selon la colonne status', function () {
    $context = createClientCategoryContext();

    $this->actingAs($context['user'])
        ->get(route('client.categories.create'))
        ->assertOk()
        ->assertSee('name="status"', false)
        ->assertDontSee('name="etablissement_id"', false);

    $this->post(route('client.categories.store'), [
        'nom' => 'Nouvelle catégorie client',
        'description' => 'Créée depuis la gestion client',
    ])->assertRedirect(route('client.categories.index'));

    $category = Category::where('nom', 'Nouvelle catégorie client')->firstOrFail();
    expect($category->status)->toBeTrue();

    $this->post(route('client.categories.toggle-status', $category->id))
        ->assertRedirect();
    expect($category->fresh()->status)->toBeFalse();

    $this->put(route('client.categories.update', $category->id), [
        'nom' => 'Catégorie client modifiée',
        'description' => 'Statut réactivé',
        'status' => 1,
    ])->assertRedirect(route('client.categories.index'));
    expect($category->fresh()->status)->toBeTrue();
});
