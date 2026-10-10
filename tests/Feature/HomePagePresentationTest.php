<?php

it('affiche uniquement la recherche et les publicités sur l’accueil', function () {
    $this->get(route('welcome'))
        ->assertOk()
        ->assertSee('id="home-query"', false)
        ->assertSee('Publicités des établissements')
        ->assertDontSee('reservation-product-choice')
        ->assertDontSee('delivery-order-form')
        ->assertDontSee('Réserver des articles');
});