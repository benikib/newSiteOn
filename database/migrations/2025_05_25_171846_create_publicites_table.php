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
        Schema::create('publicites', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->text('description')->nullable();
            $table->date('date');
            $table->foreignId('etablissement_id')
                  ->constrained('etablissements')
                  ->onDelete('cascade');
            $table->integer( 'dure');
            $table->string('status')->nullable(); // Statut de la publicité (actif/inactif/etc.)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publicites');
    }
};
