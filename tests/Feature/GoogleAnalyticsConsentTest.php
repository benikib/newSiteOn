<?php

use App\Models\Etablissement;
use App\Models\User;

it('inclut le consentement GA4 sur les pages publiques ciblées en production', function () {
    config([
        'app.env' => 'production',
        'services.analytics.ga4_measurement_id' => 'G-TEST123456',
    ]);
    app()->instance('env', 'production');
    $etablissement = Etablissement::factory()->create();

    $this->get(route('welcome'))
        ->assertOk()
        ->assertSee('bisika-cookie-consent')
        ->assertSee(route('products.index'))
        ->assertSee('G-TEST123456')
        ->assertSee('Accepter')
        ->assertSee('Refuser');

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('bisika-cookie-consent')
        ->assertSee(route('products.index'));

    $this->get(route('search'))
        ->assertOk()
        ->assertSee('bisika-cookie-consent')
        ->assertSee(route('products.index'));

    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('bisika-cookie-consent')
        ->assertSee(route('products.index'));

    $this->get(route('ets.info', $etablissement))
        ->assertOk()
        ->assertSee('bisika-cookie-consent')
        ->assertSee('G-TEST123456');
});

it('n’inclut pas GA4 sur les pages connectées ni en développement', function () {
    config([
        'app.env' => 'production',
        'services.analytics.ga4_measurement_id' => 'G-TEST123456',
    ]);
    app()->instance('env', 'production');

    $this->actingAs(User::factory()->create())
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertDontSee('bisika-cookie-consent')
        ->assertDontSee('G-TEST123456');

    config(['app.env' => 'local']);
    app()->instance('env', 'local');
    $this->get(route('welcome'))
        ->assertOk()
        ->assertDontSee('bisika-cookie-consent')
        ->assertDontSee('G-TEST123456');
});

it('n’inclut pas GA4 quand aucun identifiant de mesure n’est configuré', function () {
    config([
        'app.env' => 'production',
        'services.analytics.ga4_measurement_id' => null,
    ]);
    app()->instance('env', 'production');

    $this->get(route('welcome'))
        ->assertOk()
        ->assertDontSee('bisika-cookie-consent')
        ->assertDontSee('googletagmanager.com');
});