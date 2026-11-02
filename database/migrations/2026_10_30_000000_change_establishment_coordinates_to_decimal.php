<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('etablissements', 'latitude')) {
            Schema::table('etablissements', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable()->change();
            });
        } else {
            Schema::table('etablissements', function (Blueprint $table) {
                $table->decimal('latitude', 10, 7)->nullable();
            });
        }

        if (Schema::hasColumn('etablissements', 'longitude')) {
            Schema::table('etablissements', function (Blueprint $table) {
                $table->decimal('longitude', 11, 7)->nullable()->change();
            });
        } else {
            Schema::table('etablissements', function (Blueprint $table) {
                $table->decimal('longitude', 11, 7)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('etablissements', 'latitude')) {
            Schema::table('etablissements', function (Blueprint $table) {
                $table->float('latitude')->nullable()->change();
            });
        }

        if (Schema::hasColumn('etablissements', 'longitude')) {
            Schema::table('etablissements', function (Blueprint $table) {
                $table->float('longitude')->nullable()->change();
            });
        }
    }
};
