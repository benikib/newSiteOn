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
     Schema::create('photos', function (Blueprint $table) {
    $table->id();
    $table->string('titre'); // Champ mentionné dans votre interface
    $table->text('description')->nullable(); // Champ mentionné dans votre interface
    $table->string('image_path'); // Champ utilisé dans votre code (storage path)
    $table->string('url')->unique(); // Obligatoire
    $table->string('alt_text')->nullable(); // Nullable
    $table->string('status'); // Statut de la photo (actif/inactif/etc.)

    // Clés étrangères
    $table->foreignId('etablissement_id')
        ->nullable()
        ->constrained('etablissements')
        ->onDelete('cascade');

    $table->foreignId('service_id')
        ->nullable()
        ->constrained('services')
        ->onDelete('cascade');

    $table->foreignId('promotion_id')
        ->nullable()
        ->constrained('promotions')
        ->onDelete('cascade');

    

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
