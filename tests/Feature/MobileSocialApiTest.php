<?php

use App\Models\Etablissement;
use App\Models\Publication;
use App\Models\Publicite;
use App\Models\Service;
use App\Models\TypeEtablissement;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

it('registers mobile business accounts and keeps them pending until approval', function () {
    $type = TypeEtablissement::factory()->create();

    $this->postJson('/api/v1/auth/register', [
        'name' => 'Gestionnaire mobile',
        'email' => 'mobile-registration@bisika.test',
        'password' => 'Password-12345',
        'password_confirmation' => 'Password-12345',
        'type_compte' => 'entite_formalisee',
        'nom_entite' => 'Etablissement mobile',
        'telephone' => '+243800000001',
        'type_etablissement_id' => $type->id,
        'ville' => 'Kinshasa',
        'commune' => 'Gombe',
    ])->assertCreated()
        ->assertJsonPath('data.user.role', 'etablissement')
        ->assertJsonPath('data.user.is_approved', false)
        ->assertJsonPath('data.user.identifiant', fn ($identifier) => str_starts_with($identifier, 'BIS-'));

    $user = User::where('email', 'mobile-registration@bisika.test')->firstOrFail();
    expect($user->etablissements()->firstOrFail()->nom)->toBe('Etablissement mobile');

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'Password-12345',
        'device_name' => 'android',
    ])->assertForbidden();
});

it('filters mobile establishment search by service and exact location', function () {
    $type = TypeEtablissement::factory()->create();
    $matching = Etablissement::factory()->create([
        'statut' => 'actif',
        'type_etablissement_id' => $type->id,
        'ville' => 'Kinshasa',
        'commune' => 'Gombe',
    ]);
    $other = Etablissement::factory()->create([
        'statut' => 'actif',
        'type_etablissement_id' => $type->id,
        'ville' => 'Kinshasa',
        'commune' => 'Ngaliema',
    ]);
    Service::factory()->create([
        'etablissement_id' => $matching->id,
        'nom' => 'Réparation mobile',
        'disponibilite' => true,
    ]);
    Service::factory()->create([
        'etablissement_id' => $other->id,
        'nom' => 'Réparation mobile',
        'disponibilite' => true,
    ]);

    $this->getJson('/api/v1/public/etablissements?service=Réparation&ville=kinshasa&commune=GOMBE')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $matching->id);
});

it('serves only due publications visible to the authenticated user domains and locations', function () {
    $publisher = User::factory()->admin()->create();
    $recipient = User::factory()->gerant()->create();
    $otherRecipient = User::factory()->gerant()->create();
    $matchingType = TypeEtablissement::factory()->create();
    $otherType = TypeEtablissement::factory()->create();
    $matchingEstablishment = Etablissement::factory()->create([
        'type_etablissement_id' => $matchingType->id,
        'ville' => 'Kinshasa',
        'commune' => 'Gombe',
    ]);
    $otherEstablishment = Etablissement::factory()->create([
        'type_etablissement_id' => $otherType->id,
        'ville' => 'Kinshasa',
        'commune' => 'Ngaliema',
    ]);
    $otherRecipientEstablishment = Etablissement::factory()->create([
        'type_etablissement_id' => $matchingType->id,
        'ville' => 'Lubumbashi',
        'commune' => 'Kenya',
    ]);
    $recipient->etablissements()->attach($matchingEstablishment);
    $otherRecipient->etablissements()->attach($otherEstablishment);
    $otherRecipient->etablissements()->attach($otherRecipientEstablishment);

    $token = $recipient->createToken('test-device')->plainTextToken;
    $this->withToken($token)->postJson('/api/v1/publications', [
        'titre' => 'Annonce mobile ciblée',
        'contenu' => 'Information réservée au domaine et à la commune.',
        'categorie' => 'Rappel',
        'cible' => 'domaines',
        'types_etablissement' => [$matchingType->id],
        'ville' => 'Kinshasa',
        'commune' => 'Gombe',
    ])->assertForbidden();

    $publisherToken = $publisher->createToken('admin-device')->plainTextToken;
    Auth::forgetGuards();
    $publicationResponse = $this->withToken($publisherToken)->postJson('/api/v1/publications', [
        'titre' => 'Annonce mobile ciblée',
        'contenu' => 'Information réservée au domaine et à la commune.',
        'categorie' => 'Rappel',
        'cible' => 'domaines',
        'types_etablissement' => [$matchingType->id],
        'ville' => 'Kinshasa',
        'commune' => 'Gombe',
    ]);
    $publicationResponse->assertCreated();

    $this->postJson('/api/v1/publications', [
        'titre' => 'Annonce avec critères séparés',
        'contenu' => 'Le domaine et la localité doivent correspondre au même établissement.',
        'categorie' => 'News',
        'cible' => 'domaines',
        'types_etablissement' => [$matchingType->id],
        'ville' => 'Kinshasa',
        'commune' => 'Ngaliema',
    ])->assertCreated();

    Auth::forgetGuards();
    $this->withToken($recipient->createToken('reader')->plainTextToken)
        ->getJson('/api/v1/feed')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Annonce mobile ciblée');

    Auth::forgetGuards();
    $this->withToken($otherRecipient->createToken('reader')->plainTextToken)
        ->getJson('/api/v1/feed')
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('exposes private messaging, read status, notifications and location-based mobile advertisements', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();
    $etablissement = Etablissement::factory()->create();
    $general = Publicite::create([
        'titre' => 'Campagne générale',
        'description' => 'Visible partout',
        'date' => today(),
        'etablissement_id' => $etablissement->id,
        'dure' => 3,
        'image_path' => 'ads/general.jpg',
        'status' => 'active',
        'ciblage' => 'generalisee',
    ]);
    Publicite::create([
        'titre' => 'Campagne Gombe',
        'date' => today(),
        'etablissement_id' => $etablissement->id,
        'dure' => 3,
        'image_path' => 'ads/gombe.jpg',
        'status' => 'active',
        'ciblage' => 'emplacement',
        'ville' => 'Kinshasa',
        'commune' => 'Gombe',
    ]);
    Publicite::create([
        'titre' => 'Campagne éloignée',
        'date' => today(),
        'etablissement_id' => $etablissement->id,
        'dure' => 3,
        'image_path' => 'ads/ailleurs.jpg',
        'status' => 'active',
        'ciblage' => 'emplacement',
        'ville' => 'Lubumbashi',
        'commune' => 'Kenya',
    ]);
    $publication = Publication::create([
        'auteur_id' => $sender->id,
        'titre' => 'Notification mobile',
        'contenu' => 'Annonce de test',
        'cible' => 'tous',
        'categorie' => 'News',
        'publie_le' => now(),
    ]);
    $notification = $receiver->notifications()->create([
        'id' => (string) \Illuminate\Support\Str::uuid(),
        'type' => 'publication',
        'data' => ['publication_id' => $publication->id],
    ]);

    $senderToken = $sender->createToken('sender')->plainTextToken;
    $this->withToken($senderToken)
        ->postJson('/api/v1/messages/'.$receiver->id, ['body' => 'Bonjour'])
        ->assertCreated()
        ->assertJsonPath('data.body', 'Bonjour');

    Auth::forgetGuards();
    $receiverToken = $receiver->createToken('receiver')->plainTextToken;
    $this->withToken($receiverToken)
        ->getJson('/api/v1/messages/conversations')
        ->assertOk()
        ->assertJsonPath('data.0.unread_count', 1);
    $this->getJson('/api/v1/messages/'.$sender->id)
        ->assertOk()
        ->assertJsonPath('data.0.body', 'Bonjour');

    expect($receiver->messagesReceived()->firstOrFail()->fresh()->read_at)->not->toBeNull();

    $this->getJson('/api/v1/public/publicites?ville=Kinshasa&commune=Gombe')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['title' => 'Campagne générale'])
        ->assertJsonFragment(['title' => 'Campagne Gombe'])
        ->assertJsonMissing(['title' => 'Campagne éloignée']);

    Auth::forgetGuards();
    $this->withToken($receiver->createToken('notifications')->plainTextToken)
        ->patchJson('/api/v1/notifications/'.$notification->id.'/read')
        ->assertOk();
    expect($notification->fresh()->read_at)->not->toBeNull();
});
