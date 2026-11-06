<?php

use App\Models\Etablissement;
use App\Models\Publicite;
use App\Models\TypeEtablissement;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

it('registers formalized and informal entities with generated identifiers and pending approval', function () {
    $type = TypeEtablissement::factory()->create();

    foreach ([
        'entite_formalisee' => 'formalisee',
        'entite_non_formalisee' => 'clandestine',
    ] as $accountType => $nature) {
        $email = $accountType.'@bisika.test';
        $this->post(route('register'), [
            'name' => 'Gestionnaire '.$nature,
            'email' => $email,
            'password' => 'Password-12345',
            'password_confirmation' => 'Password-12345',
            'type_compte' => $accountType,
            'nom_entite' => 'Structure '.$nature,
            'type_etablissement_id' => $type->id,
            'telephone' => '+243 800 000 000',
            'ville' => 'Kinshasa',
            'commune' => 'Gombe',
        ])->assertRedirect(route('login'));

        $user = User::where('email', $email)->firstOrFail();
        $etablissement = $user->usersEtablissements()->with('etablissement')->firstOrFail()->etablissement;
        expect($user->role)->toBe('etablissement')
            ->and($user->is_approved)->toBeFalse()
            ->and($user->identifiant)->toStartWith('BIS-')
            ->and($etablissement->nom)->toBe('Structure '.$nature)
            ->and($etablissement->nature_entite)->toBe($nature)
            ->and($etablissement->statut)->toBe('en_attente');
    }
});

it('registers patronal organizations and links them to their generated account identifiers', function () {
    $this->post(route('register'), [
        'name' => 'Gestionnaire organisation',
        'email' => 'organisation@bisika.test',
        'password' => 'Password-12345',
        'password_confirmation' => 'Password-12345',
        'type_compte' => 'organisation_patronale',
        'nom_entite' => 'Association des entreprises',
        'telephone' => '+243 800 000 001',
        'ville' => 'Kinshasa',
    ])->assertRedirect(route('login'));

    $user = User::where('email', 'organisation@bisika.test')->firstOrFail();
    expect($user->role)->toBe('organisation_patronale')
        ->and($user->identifiant)->not->toBeEmpty()
        ->and($user->organisationPatronale()->firstOrFail()->nom)->toBe('Association des entreprises');
});

it('lets administrators grant publishing access and sends a targeted post only to matching accounts', function () {
    $admin = User::factory()->admin()->create(['is_approved' => true]);
    $publisher = User::factory()->create(['role' => 'client', 'can_publish' => false]);
    $type = TypeEtablissement::factory()->create();
    $otherType = TypeEtablissement::factory()->create();
    $matchingUser = User::factory()->gerant()->create(['is_approved' => true]);
    $otherUser = User::factory()->gerant()->create(['is_approved' => true]);
    $pendingUser = User::factory()->gerant()->create(['is_approved' => false]);

    foreach ([
        [$matchingUser, $type, 'Kinshasa', 'Gombe'],
        [$otherUser, $otherType, 'Kinshasa', 'Ngaliema'],
        [$pendingUser, $type, 'Kinshasa', 'Gombe'],
    ] as [$user, $activityType, $city, $commune]) {
        $etablissement = Etablissement::factory()->create([
            'type_etablissement_id' => $activityType->id,
            'ville' => $city,
            'commune' => $commune,
        ]);
        $user->usersEtablissements()->create(['etablissement_id' => $etablissement->id]);
    }

    $this->actingAs($publisher)
        ->get(route('publications.create'))
        ->assertForbidden();

    $this->actingAs($admin)
        ->patch(route('users.publishing', $publisher), ['can_publish' => true])
        ->assertRedirect();
    expect($publisher->refresh()->can_publish)->toBeTrue();

    $this->actingAs($publisher)
        ->post(route('publications.store'), [
            'titre' => 'Alerte du secteur',
            'categorie' => 'Rappel',
            'contenu' => 'Information réservée au domaine et à la commune.',
            'cible' => 'domaines',
            'types_etablissement' => [$type->id],
            'ville' => 'Kinshasa',
            'commune' => 'Gombe',
        ])
        ->assertRedirect(route('publications.manage'));

    expect(DB::table('notifications')->where('notifiable_id', $matchingUser->id)->count())->toBe(1)
        ->and(DB::table('notifications')->where('notifiable_id', $otherUser->id)->count())->toBe(0)
        ->and(DB::table('notifications')->where('notifiable_id', $pendingUser->id)->count())->toBe(0);

    $this->get(route('publications.index', [
        'type_id' => $type->id,
        'ville' => 'Kinshasa',
        'commune' => 'Gombe',
        'categorie' => 'Rappel',
    ]))
        ->assertOk()
        ->assertSee('Alerte du secteur')
        ->assertSee('Rappel');

    $anotherPublisher = User::factory()->create(['role' => 'client', 'can_publish' => true]);
    $this->actingAs($anotherPublisher)
        ->delete(route('publications.destroy', \App\Models\Publication::where('titre', 'Alerte du secteur')->firstOrFail()))
        ->assertNotFound();

    $this->actingAs($matchingUser)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Alerte du secteur');

    expect(DB::table('notifications')->where('notifiable_id', $matchingUser->id)->whereNotNull('read_at')->count())->toBe(1);
});

it('notifies approved users when a scheduled publication becomes due', function () {
    $publisher = User::factory()->admin()->create(['is_approved' => true]);
    $recipient = User::factory()->create(['is_approved' => true]);
    $scheduledAt = now()->addHour()->startOfMinute();

    $this->actingAs($publisher)
        ->post(route('publications.store'), [
            'titre' => 'Annonce programmée',
            'categorie' => 'News',
            'contenu' => 'Message disponible à la date prévue.',
            'cible' => 'tous',
            'publie_le' => $scheduledAt->format('Y-m-d\\TH:i'),
        ])
        ->assertRedirect(route('publications.manage'));

    expect(DB::table('notifications')->where('notifiable_id', $recipient->id)->count())->toBe(0);

    $this->travel(2)->hours();
    $this->artisan('notifications:publish-due')->assertExitCode(0);
    $this->artisan('notifications:publish-due')->assertExitCode(0);

    expect(DB::table('notifications')->where('notifiable_id', $recipient->id)->count())->toBe(1);
});

it('warns an account when its establishment identification is incomplete', function () {
    $user = User::factory()->gerant()->create(['is_approved' => true]);
    $etablissement = Etablissement::factory()->create([
        'nature_entite' => 'formalisee',
        'rccm' => null,
        'id_nat' => null,
        'nif' => null,
    ]);
    $user->usersEtablissements()->create(['etablissement_id' => $etablissement->id]);

    $this->actingAs($user)
        ->get(route('messages.index'))
        ->assertOk()
        ->assertSee('Profil à compléter')
        ->assertSee('RCCM');

    $this->get(route('profile.edit'))
        ->assertOk()
        ->assertSee($user->identifiant);
});

it('shows only generalized and matching location advertisements on the home page', function () {
    Storage::fake('public');
    $admin = User::factory()->admin()->create(['is_approved' => true]);
    $etablissement = Etablissement::factory()->create();

    foreach ([
        ['Publicité générale', 'generalisee', null, null],
        ['Publicité Kinshasa', 'emplacement', 'Kinshasa', 'Gombe'],
        ['Publicité Lubumbashi', 'emplacement', 'Lubumbashi', 'Kenya'],
    ] as [$title, $target, $city, $commune]) {
        $this->actingAs($admin)
            ->post(route('publicits.store'), [
                'titre' => $title,
                'description' => 'Campagne BISIKA',
                'date' => today()->toDateString(),
                'dure' => 5,
                'etablissement_id' => $etablissement->id,
                'status' => 'active',
                'ciblage' => $target,
                'ville' => $city,
                'commune' => $commune,
                'image' => UploadedFile::fake()->image('campagne.jpg'),
            ])
            ->assertRedirect();
    }

    $this->get(route('welcome', ['ville' => 'Kinshasa', 'commune' => 'Gombe']))
        ->assertOk()
        ->assertSee('Publicité générale')
        ->assertSee('Publicité Kinshasa')
        ->assertDontSee('Publicité Lubumbashi');

    expect(Publicite::count())->toBe(3);
});
