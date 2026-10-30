<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\MobileController;
use App\Http\Controllers\ReservationController;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Support\Facades\Route;

Route::middleware(ForceJsonResponse::class)->prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    Route::prefix('public')->group(function () {
        Route::get('establishment-types', [MobileController::class, 'establishmentTypes']);
        Route::get('etablissements', [MobileController::class, 'establishments']);
        Route::get('etablissements/{etablissement}', [MobileController::class, 'establishment']);
        Route::get('etablissements/{etablissement}/photos', [MobileController::class, 'publicEstablishmentPhotos']);
        Route::get('etablissements/{etablissement}/services', [MobileController::class, 'publicServices']);
        Route::get('etablissements/{etablissement}/products', [MobileController::class, 'publicProducts']);
        Route::post('reservations', [ReservationController::class, 'store'])->middleware('throttle:20,1');
        Route::post('product-reservations', [MobileController::class, 'createProductReservation'])->middleware('throttle:20,1');
        Route::post('delivery-orders', [MobileController::class, 'createDeliveryOrder'])->middleware('throttle:20,1');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::get('establishments', [MobileController::class, 'establishmentsForUser']);
        Route::patch('establishments/{etablissement}', [MobileController::class, 'updateCoordinates'])
            ->middleware('mobile.establishment');

        Route::prefix('establishments/{etablissement}')
            ->middleware('mobile.establishment')
            ->group(function () {
                Route::post('photo', [MobileController::class, 'uploadEstablishmentPhoto']);
                Route::delete('photo', [MobileController::class, 'deleteEstablishmentPhoto']);
                Route::get('photos', [MobileController::class, 'establishmentPhotos']);
                Route::post('photos', [MobileController::class, 'uploadEstablishmentPhotos']);
                Route::patch('photos/{photo}', [MobileController::class, 'updateEstablishmentPhoto']);
                Route::delete('photos/{photo}', [MobileController::class, 'deleteEstablishmentGalleryPhoto']);
                Route::get('dashboard', [MobileController::class, 'dashboard']);
                Route::get('categories', [MobileController::class, 'categories']);
                Route::get('units', [MobileController::class, 'units']);
                Route::get('products', [MobileController::class, 'products']);
                Route::post('products', [MobileController::class, 'createProduct']);
                Route::put('products/{product}', [MobileController::class, 'updateProduct']);
                Route::patch('products/{product}', [MobileController::class, 'updateProduct']);
                Route::post('products/{product}/image', [MobileController::class, 'uploadProductImage']);
                Route::delete('products/{product}/image', [MobileController::class, 'deleteProductImage']);
                Route::get('stocks', [MobileController::class, 'stocks']);
                Route::get('movements', [MobileController::class, 'movements']);
                Route::post('movements', [MobileController::class, 'createMovement']);
                Route::post('inventories', [MobileController::class, 'createInventory']);
                Route::get('sales/products', [MobileController::class, 'saleProducts']);
                Route::post('sales', [MobileController::class, 'createSale']);
                Route::get('sales', [MobileController::class, 'sales']);
                Route::get('reservations/services', [MobileController::class, 'serviceReservations']);
                Route::patch('reservations/services/{reservation}', [MobileController::class, 'updateServiceReservation']);
                Route::get('reservations/products', [MobileController::class, 'productReservations']);
                Route::patch('reservations/products/{id}', [MobileController::class, 'updateProductReservation']);
                Route::get('delivery-orders', [MobileController::class, 'deliveryOrders']);
                Route::patch('delivery-orders/{id}', [ReservationController::class, 'manageDelivery']);
            });
    });
});
