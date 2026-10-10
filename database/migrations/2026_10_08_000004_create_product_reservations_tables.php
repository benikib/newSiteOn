<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etablissement_id')->constrained()->restrictOnDelete();
            $table->string('client_name', 100);
            $table->string('client_phone', 25);
            $table->string('statut', 20)->default('en_attente')->index();
            $table->timestamps();
            $table->index(['etablissement_id', 'created_at']);
        });

        Schema::create('product_reservation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_reservation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->timestamps();
            $table->index(['product_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reservation_items');
        Schema::dropIfExists('product_reservations');
    }
};