<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('selling_price_original', 12, 2)->nullable();
            $table->string('selling_currency', 3)->default('CDF');
            $table->decimal('selling_exchange_rate', 12, 4)->nullable();
        });

        Schema::table('movements', function (Blueprint $table) {
            $table->decimal('selling_price_original', 12, 2)->nullable();
            $table->string('selling_currency', 3)->default('CDF');
            $table->decimal('selling_exchange_rate', 12, 4)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropColumn(['selling_price_original', 'selling_currency', 'selling_exchange_rate']);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn(['selling_price_original', 'selling_currency', 'selling_exchange_rate']);
        });
    }
};