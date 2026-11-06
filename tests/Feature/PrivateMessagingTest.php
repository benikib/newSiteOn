<?php

use App\Models\Message;
use App\Models\User;

it('allows an approved account to exchange private messages with another approved account', function () {
    $sender = User::factory()->create(['name' => 'Compte expéditeur']);
    $receiver = User::factory()->create(['name' => 'Compte destinataire']);

    $this->actingAs($sender)
        ->post(route('messages.store', $receiver), ['body' => 'Bonjour, pouvons-nous échanger ?'])
        ->assertRedirect(route('messages.index', ['with' => $receiver->id]))
        ->assertSessionHasNoErrors();

    $message = Message::query()->sole();
    expect($message->sender_id)->toBe($sender->id)
        ->and($message->receiver_id)->toBe($receiver->id)
        ->and($message->body)->toBe('Bonjour, pouvons-nous échanger ?');

    $this->actingAs($receiver)
        ->get(route('messages.index', ['with' => $sender->id]))
        ->assertOk()
        ->assertSee('Bonjour, pouvons-nous échanger ?')
        ->assertSee('Compte expéditeur');

    expect($message->refresh()->read_at)->not->toBeNull();
});

it('does not expose messages from unrelated conversations', function () {
    $first = User::factory()->create();
    $second = User::factory()->create();
    $third = User::factory()->create();

    Message::create([
        'sender_id' => $first->id,
        'receiver_id' => $second->id,
        'body' => 'Message privé entre les deux premiers comptes.',
    ]);

    $this->actingAs($third)
        ->get(route('messages.index', ['with' => $second->id]))
        ->assertOk()
        ->assertDontSee('Message privé entre les deux premiers comptes.');
});

it('paginates conversation history and marks only displayed incoming messages as read', function () {
    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    foreach (range(1, 51) as $number) {
        Message::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'body' => sprintf('Historique %03d', $number),
        ]);
    }

    $this->actingAs($receiver)
        ->get(route('messages.index', ['with' => $sender->id]))
        ->assertOk()
        ->assertSee('Historique 002')
        ->assertSee('Historique 051')
        ->assertDontSee('Historique 001');

    expect(Message::where('receiver_id', $receiver->id)->whereNotNull('read_at')->count())->toBe(50);

    $this->get(route('messages.index', ['with' => $sender->id, 'page' => 2]))
        ->assertOk()
        ->assertSee('Historique 001');

    expect(Message::where('receiver_id', $receiver->id)->whereNotNull('read_at')->count())->toBe(51);
});

it('prevents messaging oneself or an unapproved account', function () {
    $sender = User::factory()->create();
    $pending = User::factory()->create(['is_approved' => false]);

    $this->actingAs($sender)
        ->post(route('messages.store', $sender), ['body' => 'Message à moi-même'])
        ->assertNotFound();

    $this->post(route('messages.store', $pending), ['body' => 'Message au compte en attente'])
        ->assertNotFound();

    expect(Message::count())->toBe(0);
});

it('lets an approved user find other approved accounts by name', function () {
    $sender = User::factory()->create(['name' => 'Recherche']);
    User::factory()->create(['name' => 'Entreprise recherchée']);
    User::factory()->create(['name' => 'Entreprise en attente', 'is_approved' => false]);

    $this->actingAs($sender)
        ->get(route('messages.index', ['q' => 'Entreprise']))
        ->assertOk()
        ->assertSee('Entreprise recherchée')
        ->assertDontSee('Entreprise en attente');
});
