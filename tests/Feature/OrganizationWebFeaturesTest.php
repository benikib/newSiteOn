<?php

use App\Models\Etablissement;
use App\Models\OrganisationPatronale;
use App\Models\Publication;
use App\Models\TypeEtablissement;
use App\Models\User;
use App\Models\Abonnement;
use App\Models\Publicite;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

it('allows administrators to validate newly registered user accounts', function () {
    $admin = User::factory()->admin()->create(['is_approved' => true]);
    $user = User::factory()->create(['is_approved' => false]);

    $this->actingAs($admin)
        ->patch(route('users.approval', $user), ['is_approved' => true])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($user->refresh()->is_approved)->toBeTrue();
});

it('requires administrator approval before a newly registered account can sign in', function () {
    $this->post(route('register'), [
        'name' => 'Nouveau compte',
        'email' => 'nouveau@example.test',
        'password' => 'Password-12345',
        'password_confirmation' => 'Password-12345',
    ])->assertRedirect(route('login'));

    $user = User::where('email', 'nouveau@example.test')->firstOrFail();
    expect($user->is_approved)->toBeFalse();

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'Password-12345',
    ])->assertSessionHasErrors('email');
});

it('redirects an approved patronal account to its dedicated workspace after login', function () {
    $user = User::factory()->create([
    'role' => 'organisation_patronale',
    'is_approved' => true,
    'password' => bcrypt('secret-password'),
    ]);
    OrganisationPatronale::create(['user_id' => $user->id, 'nom' => 'Organisation test']);

    $this->post(route('login'), [
    'email' => $user->email,
    'password' => 'secret-password',
    ])->assertRedirect(route('organisation.dashboard'));
});

it('allows a patronal organization to update its own profile', function () {
    $user = User::factory()->create(['role' => 'organisation_patronale', 'is_approved' => true]);
    $organisation = OrganisationPatronale::create(['user_id' => $user->id, 'nom' => 'Ancien nom']);

    $this->actingAs($user)
        ->patch(route('organisation.profil.update'), [
            'nom' => 'Nouvelle organisation',
            'telephone' => '+243 800 000 000',
            'email' => 'contact@organisation.test',
            'adresse' => 'Kinshasa, Gombe',
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($organisation->refresh()->nom)->toBe('Nouvelle organisation')
        ->and($organisation->telephone)->toBe('+243 800 000 000')
        ->and($organisation->email)->toBe('contact@organisation.test')
        ->and($organisation->adresse)->toBe('Kinshasa, Gombe');
});

it('lets patronal organizations approve members and record partial and complete dues payments', function () {
    $user = User::factory()->create(['role' => 'organisation_patronale', 'is_approved' => true]);
    $organisation = OrganisationPatronale::create(['user_id' => $user->id, 'nom' => 'Organisation test']);
    $etablissement = Etablissement::factory()->create([
        'organisation_patronale_id' => $organisation->id,
        'affiliation_statut' => 'en_attente',
    ]);

    $this->actingAs($user)
        ->get(route('organisation.dashboard'))
        ->assertOk();

    $this->actingAs($user)
        ->patch(route('organisation.membres.affiliation', $etablissement), ['affiliation_statut' => 'approuvee'])
        ->assertRedirect()
        ->assertSessionHasNoErrors();
    expect($etablissement->refresh()->affiliation_statut)->toBe('approuvee');

    $this->post(route('organisation.cotisations.store'), [
        'etablissement_id' => $etablissement->id,
        'libelle' => 'Cotisation annuelle',
        'montant' => 100,
        'devise' => 'CDF',
        'date_echeance' => today()->addMonth()->toDateString(),
    ])->assertRedirect();

    $cotisation = $organisation->cotisations()->firstOrFail();
    $this->post(route('organisation.cotisations.paiements.store', $cotisation), [
        'montant' => 40,
        'date_paiement' => today()->toDateString(),
        'mode' => 'cash',
    ])->assertRedirect();
    expect($cotisation->refresh()->statut)->toBe('partielle');

    $this->post(route('organisation.cotisations.paiements.store', $cotisation), [
        'montant' => 60,
        'date_paiement' => today()->toDateString(),
        'mode' => 'transfer',
    ])->assertRedirect();
    expect($cotisation->refresh()->statut)->toBe('payee');

    $this->post(route('organisation.publications.store'), [
        'titre' => 'Information réservée au secteur',
        'contenu' => 'Annonce aux membres du domaine.',
        'cible' => 'domaines',
        'types_etablissement' => [TypeEtablissement::factory()->create()->id],
    ])->assertRedirect();
    expect($organisation->publications()->where('titre', 'Information réservée au secteur')->exists())->toBeTrue();
});

it('allows an establishment manager to submit its legal profile and request affiliation', function () {
    $user = User::factory()->gerant()->create(['is_approved' => true]);
    $etablissement = Etablissement::factory()->create();
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
        'motif' => 'Test affiliation',
        'created_by' => $user->id,
    ]);
    $organisationUser = User::factory()->create(['role' => 'organisation_patronale', 'is_approved' => true]);
    $organisation = OrganisationPatronale::create(['user_id' => $organisationUser->id, 'nom' => 'Organisation test']);

    $this->actingAs($user)
        ->put(route('etablissements.identification.update', $etablissement), [
            'nature_entite' => 'formalisee',
            'rccm' => 'CD/KIN/RCCM/123',
            'id_nat' => '01-A12345',
            'organisation_patronale_id' => $organisation->id,
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    expect($etablissement->refresh()->rccm)->toBe('CD/KIN/RCCM/123')
        ->and($etablissement->affiliation_statut)->toBe('en_attente');
});

it('shows general and matching activity-domain publications in the public news feed', function () {
    $user = User::factory()->create(['role' => 'organisation_patronale']);
    $organisation = OrganisationPatronale::create(['user_id' => $user->id, 'nom' => 'Organisation test']);
    $targetType = TypeEtablissement::factory()->create();
    $otherType = TypeEtablissement::factory()->create();

    $general = $organisation->publications()->create([
        'titre' => 'Information générale',
        'contenu' => 'Pour tous les membres.',
        'cible' => 'tous',
        'publie_le' => now()->subMinute(),
    ]);
    $targeted = $organisation->publications()->create([
        'titre' => 'Information ciblée',
        'contenu' => 'Pour un domaine.',
        'cible' => 'domaines',
        'publie_le' => now()->subMinute(),
    ]);
    $targeted->typesEtablissement()->attach($targetType);

    $excluded = $organisation->publications()->create([
        'titre' => 'Information confidentielle',
        'contenu' => 'Autre domaine.',
        'cible' => 'domaines',
        'publie_le' => now()->subMinute(),
    ]);
    $excluded->typesEtablissement()->attach($otherType);

    $this->get(route('publications.index', ['type_id' => $targetType->id]))
        ->assertOk()
        ->assertSee($general->titre)
        ->assertSee($targeted->titre)
        ->assertDontSee($excluded->titre);
});

it('allows administrators to create multimedia advertisements and services with comments and galleries', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create(['is_approved' => true]);
    $etablissement = Etablissement::factory()->create();

    $this->actingAs($admin)
        ->post(route('publicits.store'), [
            'titre' => 'Campagne image',
            'description' => 'Synopsis de la publicité',
            'date' => today()->toDateString(),
            'dure' => 7,
            'etablissement_id' => $etablissement->id,
            'status' => 'active',
            'image' => UploadedFile::fake()->image('campagne.jpg'),
        ])
        ->assertRedirect();

    $publicite = Publicite::firstOrFail();
    expect($publicite->description)->toBe('Synopsis de la publicité');
    Storage::disk('public')->assertExists($publicite->image_path);

    $this->post(route('services.store', $etablissement->id), [
        'nom' => 'Service test',
        'commentaire' => 'Commentaire affiché avec le service.',
        'etablissement_id' => $etablissement->id,
        'prix' => 15,
        'disponibilite' => true,
    ])->assertRedirect();

    $service = Service::firstOrFail();
    expect($service->commentaire)->toBe('Commentaire affiché avec le service.');

    $this->get(route('services.photos', $service))->assertOk()->assertSee('Galerie');
});
