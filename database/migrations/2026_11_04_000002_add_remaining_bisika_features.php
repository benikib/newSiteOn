<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identifiant')->nullable()->unique();
            $table->boolean('can_publish')->default(false);
        });

        DB::table('users')->whereNull('identifiant')->orderBy('id')->eachById(function ($user) {
            DB::table('users')->where('id', $user->id)->update([
                'identifiant' => 'BIS-' . str_pad((string) $user->id, 8, '0', STR_PAD_LEFT),
            ]);
        });

        Schema::table('publications', function (Blueprint $table) {
            $table->foreignId('auteur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('categorie', 30)->default('News');
            $table->string('ville')->nullable();
            $table->string('commune')->nullable();
            $table->boolean('notifications_envoyees')->default(false);
            $table->foreignId('organisation_patronale_id')->nullable()->change();
        });
        DB::table('publications')->update(['notifications_envoyees' => true]);

        Schema::table('publicites', function (Blueprint $table) {
            $table->string('ciblage', 20)->default('generalisee');
            $table->string('ville')->nullable();
            $table->string('commune')->nullable();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');

        DB::table('publications')->whereNull('organisation_patronale_id')->delete();

        Schema::table('publicites', function (Blueprint $table) {
            $table->dropColumn(['ciblage', 'ville', 'commune']);
        });

        Schema::table('publications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('auteur_id');
            $table->dropColumn(['categorie', 'ville', 'commune', 'notifications_envoyees']);
            $table->foreignId('organisation_patronale_id')->nullable(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['identifiant']);
            $table->dropColumn(['identifiant', 'can_publish']);
        });
    }
};
