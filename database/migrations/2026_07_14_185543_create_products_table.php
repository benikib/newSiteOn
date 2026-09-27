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
     Schema::create('products', function (Blueprint $table) {

    $table->id();

    $table->foreignId('etablissement_id')
        ->constrained()
        ->cascadeOnUpdate()
        ->cascadeOnDelete();

    $table->foreignId('category_id')
        ->constrained()
        ->cascadeOnUpdate()
        ->restrictOnDelete();

    $table->foreignId('unit_id')
        ->constrained()
        ->cascadeOnUpdate()
        ->restrictOnDelete();

    $table->string('code');

    $table->string('barcode')->nullable();

    $table->string('name');

    $table->text('description')->nullable();

    $table->string('image')->nullable();

    $table->boolean('status')->default(true);

    $table->timestamps();

    // Unicité dans un établissement
    $table->unique(['etablissement_id', 'code']);
    $table->unique(['etablissement_id', 'barcode']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
