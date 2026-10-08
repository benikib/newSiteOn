<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_currency', 3)->default('CDF');
            $table->decimal('payment_amount', 12, 2)->nullable();
            $table->decimal('exchange_rate', 12, 4)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_currency', 'payment_amount', 'exchange_rate']);
        });
    }
};