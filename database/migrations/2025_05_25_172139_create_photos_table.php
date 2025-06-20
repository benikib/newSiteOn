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
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->string('image_path');
            $table->foreignId('etablissement_id')
                ->nullable()
                ->constrained('etablissements')
                ->onDelete('cascade');
                $table->foreignId('service_id')
                ->nullable()
                ->constrained('services')
                ->onDelete('cascade');
            $table->integer('ordre')->default(0);
            $table->boolean('est_principale')->default(false);
            $table->boolean('est_publique')->default(true);
            $table->timestamp('date_prise')->nullable();
            $table->json('dimensions')->nullable();
            $table->string('taille')->nullable();
            $table->string('format')->default('jpg');
            $table->string('alt_text')->nullable();
            $table->string('status')->default('actif');
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
