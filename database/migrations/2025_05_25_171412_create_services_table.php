<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->text('description')->nullable();
            $table->decimal('prix', 10, 2)->nullable();
            $table->string('duree')->nullable();
            $table->boolean('disponibilite')->default(true);
            $table->foreignId('etablissement_id')
                ->constrained('etablissements')
                ->onDelete('cascade');
            $table->string('categorie')->nullable();
            $table->decimal('promotion', 5, 2)->nullable();
            $table->timestamp('date_debut_promo')->nullable();
            $table->timestamp('date_fin_promo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
