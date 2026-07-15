<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;

Route::prefix('stock')->name('stock.')->middleware(['auth', 'admins'])->group(function () {
    
    // ===== ROUTES CATÉGORIES =====
    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    // ===== ROUTES SUPPLÉMENTAIRES =====
    Route::get('categories/export', [CategoryController::class, 'export'])->name('categories.export');
    Route::post('categories/import', [CategoryController::class, 'import'])->name('categories.import');
    Route::post('categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
    Route::post('categories/{category}/move-products', [CategoryController::class, 'moveProducts'])->name('categories.move-products');
    Route::get('categories/search', [CategoryController::class, 'search'])->name('categories.search');
    Route::get('categories/stats', [CategoryController::class, 'stats'])->name('categories.stats');
    
});
use App\Http\Controllers\UnitController;


Route::resource('units', UnitController::class);



use App\Http\Controllers\ProductController;

Route::prefix('stock')->name('stock.')->middleware(['auth'])->group(function () {
    
    // ===== ROUTES PRODUITS =====
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    
    // ===== ROUTES SUPPLÉMENTAIRES =====
    Route::get('products/export', [ProductController::class, 'export'])->name('products.export');
    Route::post('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
    
});


use App\Http\Controllers\StockController;

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    
    // ===== ROUTES STOCKS =====
    Route::get('stocks/dashboard', [StockController::class, 'dashboard'])->name('stocks.dashboard');
    Route::get('stocks', [StockController::class, 'index'])->name('stocks.index');
    Route::get('stocks/create', [StockController::class, 'create'])->name('stocks.create');
    Route::post('stocks', [StockController::class, 'store'])->name('stocks.store');
    Route::get('stocks/{stock}', [StockController::class, 'show'])->name('stocks.show');
    Route::get('stocks/{stock}/edit', [StockController::class, 'edit'])->name('stocks.edit');
    Route::put('stocks/{stock}', [StockController::class, 'update'])->name('stocks.update');
    Route::delete('stocks/{stock}', [StockController::class, 'destroy'])->name('stocks.destroy');
    
    // ===== ROUTES SUPPLÉMENTAIRES =====
    Route::post('stocks/{stock}/add-quantity', [StockController::class, 'addQuantity'])->name('stocks.add-quantity');
    Route::post('stocks/{stock}/remove-quantity', [StockController::class, 'removeQuantity'])->name('stocks.remove-quantity');
    Route::get('stocks/export', [StockController::class, 'export'])->name('stocks.export');
    Route::get('stocks/search', [StockController::class, 'search'])->name('stocks.search');
    
});