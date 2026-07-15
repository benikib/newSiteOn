<?php
// Note: The filename should be corrected to routes/client.php

use App\Http\Controllers\Client\ClientStockController;
use Illuminate\Support\Facades\Route;

Route::prefix('client')->name('client.')->middleware(['auth'])->group(function () {

    // ===== TABLEAU DE BORD =====
    Route::get('dashboard', [ClientStockController::class, 'dashboard'])->name('dashboard');

    // ===== STOCKS =====
    Route::get('stocks', [ClientStockController::class, 'index'])->name('stocks.index');

    // ===== ENTRÉE DE STOCK =====
    Route::match(['get', 'post'], 'stocks/stock-in', [ClientStockController::class, 'stockIn'])->name('stocks.stock-in');

    // ===== SORTIE DE STOCK =====
    Route::match(['get', 'post'], 'stocks/stock-out', [ClientStockController::class, 'stockOut'])->name('stocks.stock-out');

    // ===== AJUSTEMENT DE STOCK =====
    Route::match(['get', 'post'], 'stocks/stock-adjust', [ClientStockController::class, 'stockAdjust'])->name('stocks.stock-adjust');

    // ===== INVENTAIRE PHYSIQUE =====
    Route::match(['get', 'post'], 'stocks/inventory', [ClientStockController::class, 'inventory'])->name('stocks.inventory');

    // ===== HISTORIQUE DES MOUVEMENTS =====
    Route::get('stocks/movements', [ClientStockController::class, 'movements'])->name('stocks.movements');

    // ===== ALERTES STOCK MINIMUM =====
    Route::get('stocks/alerts', [ClientStockController::class, 'alerts'])->name('stocks.alerts');

    // ===== PRODUITS EN RUPTURE =====
    Route::get('stocks/out-of-stock', [ClientStockController::class, 'outOfStock'])->name('stocks.out-of-stock');

    // ===== VALORISATION DU STOCK =====
    Route::get('stocks/valuation', [ClientStockController::class, 'valuation'])->name('stocks.valuation');

});

// products routes for client

Route::prefix('client')->name('client.')->middleware(['auth'])->group(function () {
    
    // ===== PRODUITS =====
    Route::get('products', [App\Http\Controllers\Client\ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [App\Http\Controllers\Client\ProductController::class, 'create'])->name('products.create');
    Route::post('products', [App\Http\Controllers\Client\ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}/edit', [App\Http\Controllers\Client\ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [App\Http\Controllers\Client\ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [App\Http\Controllers\Client\ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('products/{product}/toggle-status', [App\Http\Controllers\Client\ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    
});// Dans routes/web.php



use App\Http\Controllers\Client\SaleController;

Route::prefix('client')->name('client.')->middleware(['auth'])->group(function () {
    
    // ===== VENTES =====
    Route::get('sales/pos', [SaleController::class, 'pos'])->name('sales.pos');
    Route::post('sales/store', [SaleController::class, 'store'])->name('sales.store');
    Route::get('sales/history', [SaleController::class, 'history'])->name('sales.history');
    Route::get('sales/search', [SaleController::class, 'searchProducts'])->name('sales.search');
    Route::get('sales/stats', [SaleController::class, 'stats'])->name('sales.stats');
    
    // ===== FACTURES =====
    Route::get('sales/invoice/{id}', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::get('sales/pdf/{id}', [SaleController::class, 'generateInvoice'])->name('sales.pdf');
    Route::get('sales/print/{id}', [SaleController::class, 'printInvoice'])->name('sales.print');
    
});



use App\Http\Controllers\Client\CategoryController;

Route::prefix('client')->name('client.')->middleware(['auth'])->group(function () {
    
    // ===== CATÉGORIES =====
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    
});