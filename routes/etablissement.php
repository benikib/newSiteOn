<?php

use App\Http\Controllers\PromotionController;
use App\Http\Controllers\PubliciteController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EtablissementController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\TypeEtablissementController;
use App\Http\Controllers\userController;
use App\Http\Controllers\UserEtablissementController;
use App\Models\Etablissement;
use App\Models\Promotion;
use App\Models\Publicite;
use App\Models\User;
Route::middleware(['auth', 'etablissements'])->group(function () {
Route::get('etablissement/services/{etablissement}', [\App\Http\Controllers\ServiceController::class, 'indexEtablissement'])->name('etablissements.services.index');
Route::get('etablissement/service/create/{etablissement}', [\App\Http\Controllers\ServiceController::class, 'create'])->name('etablissements.services.create');
Route::post('etablissement/service/{service}', [\App\Http\Controllers\ServiceController::class, 'store'])->name('etablissements.services.store');
Route::get('etablissement/service/{service}/edit', [\App\Http\Controllers\ServiceController::class, 'edit'])->name('etablissements.services.edit');
Route::put('etablissement/service/{service}', [\App\Http\Controllers\ServiceController::class, 'update'])->name('etablissements.services.update');
Route::delete('etablissement/service/{service}', [\App\Http\Controllers\ServiceController::class, 'destroy'])->name('etablissements.services.destroy');

Route::get('etablissement/publicites/{etablissement}', [\App\Http\Controllers\PubliciteController::class, 'indexEtablissement'])->name('etablissements.publicites.index');
Route::get('etablissement/publicite/create/{etablissement}', [\App\Http\Controllers\PubliciteController::class, 'create'])->name('etablissements.publicites.create');
Route::post('etablissement/publicite/{publicite}', [\App\Http\Controllers\PubliciteController::class, 'store'])->name('etablissements.publicites.store');
Route::get('etablissement/publicite/{publicite}/edit', [\App\Http\Controllers\PubliciteController::class, 'edit'])->name('etablissements.publicites.edit');
Route::put('etablissement/publicite/{publicite}', [\App\Http\Controllers\PubliciteController::class, 'update'])->name('etablissements.publicites.update');
Route::delete('etablissement/publicite/{publicite}', [\App\Http\Controllers\PubliciteController::class, 'destroy'])->name('etablissements.publicites.destroy');


Route::get('etablissement/promotions/{etablissement}', [\App\Http\Controllers\PromotionController::class, 'indexEtablissement'])->name('etablissements.promotions.index');
Route::get('etablissement/promotion/create/{etablissement}', [\App\Http\Controllers\PromotionController::class, 'create'])->name('etablissements.promotions.create');
Route::post('etablissement/promotion/{promotion}', [\App\Http\Controllers\PromotionController::class, 'store'])->name('etablissements.promotions.store');
Route::get('etablissement/promotion/{promotion}/edit', [\App\Http\Controllers\PromotionController::class, 'edit'])->name('etablissements.promotions.edit');
Route::put('etablissement/promotion/{promotion}', [\App\Http\Controllers\PromotionController::class, 'update'])->name('etablissements.promotions.update');
Route::delete('etablissement/promotion/{promotion}', [\App\Http\Controllers\PromotionController::class, 'destroy'])->name('etablissements.promotions.destroy');
Route::get('etablissement/{etablissement}/edit', [EtablissementController::class, 'edit'])->name('etablissements.edit');

Route::get('users/etablissments', [UserEtablissementController::class, 'index'])->name('users.etablissements');
Route::get('users/dashboard', [EtablissementController::class, 'dashboard'])->name('dashboard_ets');
Route::get('users/promotion/', [UserEtablissementController::class, 'promotion'])->name('users.promotions');

Route::put('/ets/{id}', [EtablissementController::class, 'update'])->name('ets.update');
Route::post('/ets', [EtablissementController::class, 'store'])->name('ets.store');
Route::post('/ets/services/', [\App\Http\Controllers\ServiceController::class, 'store'])->name('service.store');
Route::post('/glerys', [\App\Http\Controllers\PhotoController::class, 'store'])->name('galleries.store');
Route::resource('/galleries', PhotoController::class)->only([
    'store', 'destroy'
]);
Route::resource('/promotions', PromotionController::class)->only([
    'store', 'destroy'
]);


Route::post('/publicites', [PubliciteController::class,'store'])->name('publicite.store');
Route::put( 'publicites/{user}',[PubliciteController::class,'update'])->name('publicite.update');

Route::delete('/publicites/{id}', [PubliciteController::class,'destroy'])->name('publicite.destroy');
Route::resource('promotions', PromotionController::class)->except(['show']);
});
