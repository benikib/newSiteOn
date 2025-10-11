<?php

use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TypeEtablissementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\PaiementController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\EquipeController;
use App\Http\Controllers\IntegrateurController;
use Illuminate\Support\Facades\Mail;

Route::get('/', [PhotoController::class, 'index'])->name('welcome');
//integrateur 
Route::get('/integrateur',[IntegrateurController::class,'index'])->name('index');
Route::get('/grilles', function() {
    // Vous pouvez implémenter la logique de recherche ici
    return view('partials.grillesCarte');
})->name('grilles');

Route::get('/results', [UserController::class,'search'])->
name('search');

Route::get('/contact', function() {
    // Vous pouvez implémenter la logique de contact ici
    return view('contact');
})->name('contact');
Route::post('/contact', [UserController::class, 'sendContact'])->name('contact.submit');

Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');
Route::post('/paiements/reservation', [PaiementController::class, 'paiementReservation'])->name('paiements.store');

// Route::get('/search', function() {
//     // Vous pouvez implémenter la logique de recherche ici
//     return view('welcome');
// })->name('search');

Route::get('/desc/ets/{etablissement}',[UserController::class,'ets_info'])->name('ets.info');

Route::get('etablissement/paiement', [\App\Http\Controllers\PaiementController::class, 'index'])->name('etablissements.paiements.index');
Route::post('etablissement/paiement', [\App\Http\Controllers\PaiementController::class, 'store'])->name('etablissements.paiements.store');




Route::resource('personnels', PersonnelController::class);
Route::resource('equipes', EquipeController::class);




// Routes pour les établissements
Route::prefix('etablissements')->group(function () {
    // Afficher le profil de l'établissement
    Route::get('etablissements/{etablissement}', [EtablissementController::class, 'show'])->name('etablissement.show');

    // Mettre à jour les informations de base
    Route::put('/etablissement/{etablissement}', [EtablissementController::class, 'updatetitre'])->name('etablissements.updatetitre');

    // Mettre à jour la photo de profil
    Route::post('/{etablissement}/update-photo', [EtablissementController::class, 'updatePhoto'])->name('etablissements.updatePhoto');

    // Mettre à jour les contacts
    Route::put('/{etablissement}/update-contact', [EtablissementController::class, 'updateContact'])->name('etablissements.updateContact');

    // Mettre à jour la description
    Route::put('/{etablissement}/update-description', [EtablissementController::class, 'updateDescription'])->name('etablissements.updateDescription');

    // Mettre à jour l'adresse
    Route::put('/{etablissement}/update-address', [EtablissementController::class, 'updateAddress'])->name('etablissements.updateAddress');

    // Mettre à jour les réseaux sociaux
    Route::put('/{etablissement}/update-social', [EtablissementController::class, 'updateSocial'])->name('etablissements.updateSocial');
});

// Routes pour les services
Route::prefix('services')->group(function () {
    // Ajouter un nouveau service
    Route::post('/etablissements/ets', [ServiceController::class, 'store'])->name('etablissements.services.store');

    // Mettre à jour un service
    Route::put('/ets/{service}', [ServiceController::class, 'update'])->name('etablissements.services.update');

    // Supprimer un service
    Route::delete('/ets/{service}', [ServiceController::class, 'destroy'])->name('etablissements.services.destroy');
});

// Routes pour les photos
Route::prefix('photos')->group(function () {
    // Ajouter des photos
    Route::post('/', [PhotoController::class, 'stores'])->name('photos.stores');

    // Mettre à jour une photo
    Route::put('/{photo}', [PhotoController::class, 'update'])->name('photos.update');

    // Supprimer une photo
    Route::delete('/{photo}', [PhotoController::class, 'destroy'])->name('photos.destroy');
});

// Vous pouvez aussi ajouter une route pour la page d'accueil si nécessaire


// Route de fallback (optionnelle)
Route::fallback(function () {
    return abort(404);
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

Route::get('/send', function () {
Mail::to(['benikasu7@gmail.com', 'yoshuankunda1@gmail.com'])
    ->send(new \App\Mail\OrderShipped());

});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/etablissement.php';
