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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->foreignId('etablissement_id')
                ->constrained('etablissements')
                ->onDelete('cascade');
            $table->decimal('reduction', 5, 2);
            $table->timestamp('date_debut');
            $table->timestamp('date_fin');
            $table->text('conditions')->nullable();
            $table->string('code_promo')->unique();
            $table->integer('nombre_utilisations');
            $table->integer('nombre_utilisations_restantes');
            $table->boolean('est_active')->default(true);
            $table->string('type_reduction')->default('pourcentage');
            $table->decimal('montant_minimum', 10, 2)->nullable();
            $table->decimal('montant_maximum', 10, 2)->nullable();
            $table->json('jours_valides')->nullable();
            $table->json('heures_valides')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
