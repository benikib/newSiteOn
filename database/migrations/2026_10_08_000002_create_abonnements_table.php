<?php

use Carbon\CarbonImmutable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('abonnements')) {
            Schema::create('abonnements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('etablissement_id')->constrained()->restrictOnDelete();
                $table->date('date_debut');
                $table->date('date_fin');
                $table->string('statut', 20)->index();
                $table->string('type_operation', 30)->index();
                $table->text('motif');
                $table->decimal('montant_paye', 12, 2)->nullable();
                $table->string('devise', 3)->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('created_at')->useCurrent();
                $table->index(['etablissement_id', 'date_debut', 'date_fin']);
            });
        }

        $dateFin = CarbonImmutable::parse(config('abonnements.migration_end_date'))->toDateString();
        $etablissements = DB::table('etablissements')->get(['id', 'statut', 'created_at']);
        $actifs = $etablissements->where('statut', 'actif');
        $inactifsIgnores = $etablissements->count() - $actifs->count();
        $migrated = 0;

        foreach ($actifs as $etablissement) {
            $dateDebut = CarbonImmutable::parse($etablissement->created_at)->toDateString();
            if ($dateDebut > $dateFin) {
                throw new RuntimeException(
                    "La date de fin de migration {$dateFin} précède la création de l'établissement {$etablissement->id}. " .
                    'Modifiez SUBSCRIPTION_MIGRATION_END_DATE avant de relancer la migration.'
                );
            }

            $exists = DB::table('abonnements')
                ->where('etablissement_id', $etablissement->id)
                ->where('type_operation', 'migration')
                ->exists();

            if (!$exists) {
                DB::table('abonnements')->insert([
                    'etablissement_id' => $etablissement->id,
                    'date_debut' => $dateDebut,
                    'date_fin' => $dateFin,
                    'statut' => 'actif',
                    'type_operation' => 'migration',
                    'motif' => 'Abonnement initial créé lors de la migration, période de grâce',
                    'montant_paye' => null,
                    'devise' => null,
                    'created_by' => null,
                    'created_at' => now(),
                ]);
            }

            $migrated++;
        }

        if (defined('STDOUT')) {
            fwrite(STDOUT, "\nRécapitulatif migration abonnements\n");
            fwrite(STDOUT, "Établissements actifs migrés : {$migrated}\n");
            fwrite(STDOUT, "Établissements inactifs ignorés : {$inactifsIgnores}\n");
            fwrite(STDOUT, "Date de fin appliquée : {$dateFin}\n\n");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('abonnements');
    }
};