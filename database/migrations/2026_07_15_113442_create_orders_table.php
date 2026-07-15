<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            
            // Établissement
            $table->foreignId('etablissement_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            
            // Utilisateur
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            
            // Client (optionnel)
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();
            
            // Numéro de commande
            $table->string('order_number')->unique();
            
            // Informations client (si client non enregistré)
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->text('customer_address')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            
            // Montants (HT, TVA, TTC)
            $table->decimal('total_ht', 12, 2)->default(0);
            $table->decimal('total_tva', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            
            // Statut
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])
                ->default('pending');
            
            // Paiement
            $table->enum('payment_method', ['cash', 'card', 'transfer', 'other'])
                ->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])
                ->default('pending');
            
            // Type de commande
            $table->enum('type', ['pos', 'direct', 'online'])
                ->default('pos');
            
            // Date de commande
            $table->timestamp('order_date')->nullable();
            
            $table->timestamps();
            
            // Index
            $table->index(['etablissement_id', 'order_number']);
            $table->index(['status', 'created_at']);
            $table->index(['type', 'created_at']);
            $table->index('customer_id');
            $table->index('order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};