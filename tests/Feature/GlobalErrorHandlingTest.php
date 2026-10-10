<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

it('masque le détail d’une exception serveur dans une réponse HTML', function () {
    Route::get('/_tests/global-error-html', function () {
        throw new RuntimeException('SQLSTATE internal connection details');
    });

    $this->get('/_tests/global-error-html')
        ->assertStatus(500)
        ->assertSee('Une erreur est survenue. Veuillez réessayer plus tard.')
        ->assertDontSee('SQLSTATE internal connection details');
});

it('masque le détail d’une exception serveur dans une réponse JSON', function () {
    Route::get('/_tests/global-error-json', function () {
        throw new RuntimeException('internal token value');
    });

    $this->getJson('/_tests/global-error-json')
        ->assertStatus(500)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Une erreur est survenue. Veuillez réessayer plus tard.')
        ->assertDontSee('internal token value');
});

it('traduit les erreurs de validation en français sans modifier le locale du projet', function () {
    Route::post('/_tests/french-validation', function (Request $request) {
        $request->validate(['email' => 'required|email']);

        return response()->noContent();
    });

    $this->postJson('/_tests/french-validation')
        ->assertUnprocessable()
        ->assertJsonPath('errors.email.0', 'Le champ adresse e-mail est obligatoire.');
});