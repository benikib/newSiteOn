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
        Schema::create('etablissements', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->text('description')->nullable();
            $table->string('telephone', 25)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('ville', 255)->nullable();
              $table->string('quartier', 255)->nullable();
            $table->string('commune', 255)->nullable();
            $table->string('avenue', 255)->nullable();
            $table->string('numero', 100)->nullable();
            $table->foreignId('type_etablissement_id')
                ->constrained('type_etablissements')
                ->onDelete('cascade');
            $table->string('statut')->default('en_attente');
            $table->float('note_moyenne')->default(0);
            $table->integer('nombre_avis')->default(0);
            $table->integer('capacite')->nullable();
            $table->time('horaires_ouverture')->nullable();
            $table->time('horaires_fermeture')->nullable();
            $table->string('jours_ouverture')->nullable();
            $table->json('services_additionnels')->nullable();
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etablissements');
    }
};
