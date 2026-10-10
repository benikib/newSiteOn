<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('etablissements', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
            $table->text('itineraire')->nullable();
        });

        $usedSlugs = [];
        foreach (DB::table('etablissements')->orderBy('id')->get(['id', 'nom']) as $etablissement) {
            $baseSlug = Str::slug($etablissement->nom) ?: 'etablissement';
            if (is_numeric($baseSlug)) {
                $baseSlug = 'etablissement-' . $baseSlug;
            }

            $slug = $baseSlug;
            $suffix = 2;
            while (isset($usedSlugs[$slug])) {
                $slug = $baseSlug . '-' . $suffix++;
            }

            $usedSlugs[$slug] = true;
            DB::table('etablissements')->where('id', $etablissement->id)->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::table('etablissements', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'itineraire']);
        });
    }
};