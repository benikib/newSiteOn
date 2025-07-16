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
        Schema::create('reservations', function (Blueprint $table) {
             $table->id();
        $table->foreignId('service_id')->constrained();
        $table->string('client_name', 100);
        $table->string('client_phone', 25)->nullable();
        $table->string('statut')->default('en_attente');
        $table->date('date');
        

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
