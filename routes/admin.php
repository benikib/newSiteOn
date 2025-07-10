<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\PubliciteController;
use App\Http\Controllers\TypeEtablissementController;
use App\Http\Controllers\UserController;
use App\Models\Etablissement;
use App\Models\Publicite;
use App\Models\User;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TauxController;

Route::middleware(['auth', 'admins'])->group(function () {
   Route::get('/etablissement',[EtablissementController::class, 'index']) ->name('etablissements.index');
Route::get('/etablissement/create', [EtablissementController::class, 'create'])->name('etablissements.create');
Route::post('/etablissement', [EtablissementController::class, 'store'])->name('etablissements.store');
Route::get('/etablissement/{id}/edit', [EtablissementController::class, 'edit'])->name('etablissements.edit');
Route::put('/etablissement/{id}', [EtablissementController::class, 'update'])->name('etablissements.update');

Route::delete('/etablissement/{etablissement}', [EtablissementController::class, 'destroy'])->name('etablissements.destroy');
Route::get('/etablissement/{id}', [EtablissementController::class, 'show'])->name('etablissements.show');
Route::post('/etablissement/{etablissement}/note_moyenne', [EtablissementController::class, 'note_moyenne'])->name('etablissements.note_moyenne');

Route::get('/type', [TypeEtablissementController::class, 'index'])->name('type_etablissements.index');
Route::get('/type/create', [TypeEtablissementController::class, 'create'])->name('type_etablissements.create');
Route::post('/type', [TypeEtablissementController::class, 'store'])->name('type_etablissements.store');
Route::get('/type/{id}/edit', [TypeEtablissementController::class, 'edit'])->name('type_etablissements.edit');
Route::put('/type/{id}', [TypeEtablissementController::class, 'update'])->name('type_etablissements.update');
Route::delete('/type/{typeEtablissement}', [TypeEtablissementController::class, 'destroy'])->name('type_etablissements.destroy');

// service
Route::get('/service/{etablissement}', [\App\Http\Controllers\ServiceController::class, 'index'])->name('services.index');
Route::get('/service/create/{etablissement}', [\App\Http\Controllers\ServiceController::class, 'create'])->name('services.create');
Route::post('/service/{service}', [\App\Http\Controllers\ServiceController::class, 'store'])->name('services.store');
Route::get('/service/{service}/edit', [\App\Http\Controllers\ServiceController::class, 'edit'])->name('services.edit');
Route::put('/service/{service}', [\App\Http\Controllers\ServiceController::class, 'update'])->name('services.update');
Route::delete('/service/{service}', [\App\Http\Controllers\ServiceController::class, 'destroy'])->name('services.destroy');

Route::get('/users',[UserController::class,'index'])->name("users.index");
Route::get('/users/admins', [UserController::class, 'admins'])->name('admins.index');
Route::post('/users', [UserController::class,'store'])->name('users.store');
Route::put( 'user/{user}',[UserController::class,'update'])->name('users.update');
Route::get('/dashboard',[UserController::class, 'repportingAdmins'])->name('dashboard');

Route::get('/publicites',[PubliciteController::class,'index'])->name("publicites.index");
Route::post('/publicites', [PubliciteController::class,'store'])->name('publicites.store');
Route::post('/publicite', [PubliciteController::class,'store'])->name('publicits.store');
Route::put( 'publicites/{user}/etablissement',[PubliciteController::class,'update'])->name('publicites.update');


Route::get('/users_ets/{ets}',[UserController::class,'users_ets'])->name("users_ets.index");
Route::post('/users_ets', [UserController::class,'store_ets'])->name('users_ets.store');
Route::put( 'user_ets/{user}',[UserController::class,'update_ets'])->name('users_ets.update');
Route::get('/taux', [TauxController::class, 'create'])->name('taux.create');
Route::post('/taux', [TauxController::class, 'store'])->name('taux.store');
Route::get('/taux/{taux}', [TauxController::class, 'show'])->name('taux.show');

Route::put('/taux/{taux}', [TauxController::class, 'update'])->name('taux.update');
Route::delete('/taux/{taux}', [TauxController::class, 'destroy'])->name('taux.destroy');


});



