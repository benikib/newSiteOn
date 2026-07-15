<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            
            // Commande
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            
            // Produit
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            
            // Quantité
            $table->integer('quantity')->default(1);
            
            // Prix HT
            $table->decimal('price_ht', 12, 2)->default(0);
            
            // TVA
            $table->decimal('tva_rate', 5, 2)->default(20);
            $table->decimal('tva_amount', 12, 2)->default(0);
            
            // Sous-total HT
            $table->decimal('subtotal_ht', 12, 2)->default(0);
            
            // Sous-total TTC
            $table->decimal('subtotal_ttc', 12, 2)->default(0);
            
            $table->timestamps();
            
            // Index
            $table->index(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};