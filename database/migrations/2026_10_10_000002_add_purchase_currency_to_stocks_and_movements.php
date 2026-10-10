<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->decimal('purchase_price_original', 12, 2)->nullable();
            $table->string('purchase_currency', 3)->default('CDF');
            $table->decimal('purchase_exchange_rate', 12, 4)->nullable();
        });

        Schema::table('movements', function (Blueprint $table) {
            $table->decimal('purchase_price_original', 12, 2)->nullable();
            $table->string('purchase_currency', 3)->default('CDF');
            $table->decimal('purchase_exchange_rate', 12, 4)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('movements', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_price_original', 'purchase_currency', 'purchase_exchange_rate',
            ]);
        });

        Schema::table('stocks', function (Blueprint $table) {
            $table->dropColumn([
                'purchase_price_original', 'purchase_currency', 'purchase_exchange_rate',
            ]);
        });
    }
};