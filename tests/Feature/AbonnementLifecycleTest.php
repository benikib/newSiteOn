<?php

use App\Models\Abonnement;
use App\Models\Etablissement;
use App\Models\User;
use App\Services\AbonnementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    $this->travelTo(now()->parse('2026-10-08 12:00:00'));
});

it('expire automatiquement un abonnement et conserve la réactivation comme nouvelle entrée', function () {
    $admin = User::factory()->admin()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    $ancien = Abonnement::create([
        'etablissement_id' => $etablissement->id,
        'date_debut' => '2026-10-01',
        'date_fin' => '2026-10-07',
        'statut' => 'actif',
        'type_operation' => 'migration',
        'motif' => 'Période initiale',
        'created_by' => $admin->id,
    ]);

    $service = app(AbonnementService::class);
    expect($service->refreshEtablissement($etablissement))->toBeFalse();
    expect($ancien->fresh()->statut)->toBe('expire');
    expect($etablissement->fresh()->statut)->toBe('desactive');

    $nouveau = $service->create($etablissement, [
        'type_operation' => 'reactivation_manuelle',
        'date_debut' => '2026-10-08',
        'date_fin' => '2026-12-31',
        'motif' => 'Renouvellement après expiration',
    ], $admin);

    expect($nouveau->id)->not->toBe($ancien->id);
    expect($ancien->fresh()->motif)->toBe('Période initiale');
    expect($etablissement->fresh()->statut)->toBe('actif');
});

it('fait commencer un renouvellement le lendemain et refuse les chevauchements', function () {
    $admin = User::factory()->admin()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    Abonnement::create([
        'etablissement_id' => $etablissement->id,
        'date_debut' => '2026-10-01',
        'date_fin' => '2026-10-20',
        'statut' => 'actif',
        'type_operation' => 'creation',
        'motif' => 'Initial',
        'created_by' => $admin->id,
    ]);

    $service = app(AbonnementService::class);
    $renouvellement = $service->create($etablissement, [
        'type_operation' => 'renouvellement',
        'date_debut' => '2026-10-21',
        'date_fin' => '2026-11-20',
        'motif' => 'Renouvellement',
    ], $admin);

    expect($renouvellement->date_debut->toDateString())->toBe('2026-10-21');
    expect(fn () => $service->create($etablissement, [
        'type_operation' => 'renouvellement',
        'date_debut' => '2026-10-21',
        'date_fin' => '2026-10-30',
        'motif' => 'Chevauchement',
    ], $admin))->toThrow(ValidationException::class);
});

it('trace une suspension sans modifier l’abonnement existant', function () {
    $admin = User::factory()->admin()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    $abonnement = Abonnement::create([
        'etablissement_id' => $etablissement->id,
        'date_debut' => '2026-10-01',
        'date_fin' => '2026-10-30',
        'statut' => 'actif',
        'type_operation' => 'creation',
        'motif' => 'Initial',
        'created_by' => $admin->id,
    ]);

    $service = app(AbonnementService::class);
    $suspension = $service->suspend($etablissement, 'Suspension administrative', $admin);

    expect($abonnement->fresh()->statut)->toBe('actif');
    expect($suspension->type_operation)->toBe('suspension');
    expect($suspension->statut)->toBe('annule');
    expect($etablissement->fresh()->statut)->toBe('desactive');
    expect($service->currentPeriod($etablissement))->toBeNull();
});

it('refuse la connexion d’un établissement sans abonnement valide', function () {
    $user = User::factory()->gerant()->create([
        'email' => 'expired@example.com',
        'password' => bcrypt('password'),
    ]);
    $etablissement = Etablissement::factory()->create(['statut' => 'desactive']);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('affiche l’historique admin et enregistre une nouvelle période avec son auteur', function () {
    $admin = User::factory()->admin()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'desactive']);
    $this->actingAs($admin);

    $this->get(route('admin.abonnements.index', $etablissement))
        ->assertOk()
        ->assertSee('Historique des abonnements');

    $this->post(route('admin.abonnements.store', $etablissement), [
        'type_operation' => 'creation',
        'date_debut' => '2026-10-08',
        'date_fin' => '2026-12-31',
        'motif' => 'Ouverture du compte',
        'montant_paye' => '50.00',
        'devise' => 'USD',
    ])->assertRedirect(route('admin.abonnements.index', $etablissement));

    $this->assertDatabaseHas('abonnements', [
        'etablissement_id' => $etablissement->id,
        'type_operation' => 'creation',
        'motif' => 'Ouverture du compte',
        'created_by' => $admin->id,
        'montant_paye' => '50.00',
        'devise' => 'USD',
    ]);
    expect($etablissement->fresh()->statut)->toBe('actif');

    $this->get(route('admin.abonnements.pdf', $etablissement))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

it('rejoue le backfill sans doublons et ignore les établissements inactifs', function () {
    $actif = Etablissement::factory()->create([
        'statut' => 'actif',
        'created_at' => '2026-09-01 10:00:00',
    ]);
    $inactif = Etablissement::factory()->create([
        'statut' => 'desactive',
        'created_at' => '2026-09-02 10:00:00',
    ]);
    $migration = require base_path('database/migrations/2026_10_08_000002_create_abonnements_table.php');

    $migration->up();
    $migration->up();

    $this->assertDatabaseHas('abonnements', [
        'etablissement_id' => $actif->id,
        'date_debut' => '2026-09-01',
        'date_fin' => '2026-12-31',
        'statut' => 'actif',
        'type_operation' => 'migration',
        'created_by' => null,
    ]);
    $this->assertDatabaseMissing('abonnements', [
        'etablissement_id' => $inactif->id,
    ]);
    expect(Abonnement::where('etablissement_id', $actif->id)->count())->toBe(1);
});

it('déconnecte une session existante dès que la période expire', function () {
    $user = User::factory()->gerant()->create();
    $etablissement = Etablissement::factory()->create(['statut' => 'actif']);
    DB::table('user_etablissements')->insert([
        'user_id' => $user->id,
        'etablissement_id' => $etablissement->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $abonnement = Abonnement::create([
        'etablissement_id' => $etablissement->id,
        'date_debut' => '2026-10-01',
        'date_fin' => '2026-10-08',
        'statut' => 'actif',
        'type_operation' => 'creation',
        'motif' => 'Initial',
        'created_by' => $user->id,
    ]);

    $this->actingAs($user)->travelTo(now()->parse('2026-10-09 00:01:00'));
    $this->get(route('users.etablissements'))
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('email');

    expect($abonnement->fresh()->statut)->toBe('expire');
    expect($etablissement->fresh()->statut)->toBe('desactive');
    $this->assertGuest();
});