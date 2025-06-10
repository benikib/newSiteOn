<?php

use App\Http\Controllers\PhotoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TypeEtablissementController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PhotoController::class, 'index'])->name('welcome');
Route::get('/grilles', function() {
    // Vous pouvez implémenter la logique de recherche ici
    return view('partials.grillesCarte');
})->name('grilles');

Route::get('/results', [UserController::class,'search'])->
name('search');

// Route::get('/search', function() {
//     // Vous pouvez implémenter la logique de recherche ici
//     return view('welcome');
// })->name('search');

Route::get('/desc/ets/{etablissement}',[UserController::class,'ets_info'])->name('ets.info');




Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});

require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/etablissement.php';
