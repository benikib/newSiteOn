<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('movements', function (Blueprint $table) {
            $table->id();

            // Établissement
            $table->foreignId('etablissement_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Produit
            $table->foreignId('product_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Type de mouvement
            $table->enum('type', [
                'in',                    // Entrée de stock
                'out',                   // Sortie de stock
                'adjust_positive',       // Ajustement positif
                'adjust_negative',       // Ajustement négatif
                'inventory_in',          // Inventaire entrée
                'inventory_out',         // Inventaire sortie
                'stock_in_create',       // Création de stock
                'stock_in_update',       // Mise à jour de stock
            ])->default('in');

            // Quantités
            $table->integer('quantity')->default(0);
            $table->integer('before')->default(0);
            $table->integer('after')->default(0);

            // Prix
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('selling_price', 12, 2)->default(0);

            // Note
            $table->text('note')->nullable();

            // Utilisateur
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->timestamps();

            // Index pour les recherches
            $table->index(['etablissement_id', 'product_id']);
            $table->index(['type', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movements');
    }
};