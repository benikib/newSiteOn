<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('initial_amount', 12, 2)->default(0)->after('total_amount');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('initial_amount');
            $table->string('discount_type', 12)->nullable()->after('discount_amount');
            $table->decimal('discount_value', 12, 2)->nullable()->after('discount_type');
        });

        DB::table('orders')->update(['initial_amount' => DB::raw('total_amount')]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['initial_amount', 'discount_amount', 'discount_type', 'discount_value']);
        });
    }
};