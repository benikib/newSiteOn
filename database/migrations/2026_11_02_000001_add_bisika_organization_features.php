<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_approved')->default(true);
        });

        Schema::create('organisations_patronales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('telephone', 25)->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->timestamps();
        });

        Schema::table('etablissements', function (Blueprint $table) {
            $table->string('nature_entite', 20)->default('formalisee');
            $table->string('rccm')->nullable();
            $table->string('id_nat')->nullable();
            $table->string('cnss')->nullable();
            $table->string('inpp')->nullable();
            $table->string('nif')->nullable();
            $table->string('autorisation_path')->nullable();
            $table->foreignId('organisation_patronale_id')->nullable()
                ->constrained('organisations_patronales')->nullOnDelete();
            $table->string('affiliation_statut', 20)->default('aucune');
        });

        Schema::create('cotisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_patronale_id')->constrained('organisations_patronales')->cascadeOnDelete();
            $table->foreignId('etablissement_id')->constrained()->cascadeOnDelete();
            $table->string('libelle');
            $table->text('description')->nullable();
            $table->decimal('montant', 14, 2);
            $table->string('devise', 3)->default('CDF');
            $table->date('date_echeance');
            $table->string('statut', 20)->default('impayee');
            $table->timestamps();
        });

        Schema::create('paiements_cotisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotisation_id')->constrained()->cascadeOnDelete();
            $table->decimal('montant', 14, 2);
            $table->date('date_paiement');
            $table->string('mode', 40)->nullable();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('enregistre_par')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisation_patronale_id')->constrained('organisations_patronales')->cascadeOnDelete();
            $table->string('titre');
            $table->string('synopsis', 500)->nullable();
            $table->longText('contenu');
            $table->string('cible', 20)->default('tous');
            $table->timestamp('publie_le')->nullable();
            $table->timestamp('expire_le')->nullable();
            $table->timestamps();
        });

        Schema::create('publication_type_etablissement', function (Blueprint $table) {
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            $table->foreignId('type_etablissement_id')->constrained()->cascadeOnDelete();
            $table->primary(['publication_id', 'type_etablissement_id'], 'publication_type_primary');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->text('commentaire')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('services', fn (Blueprint $table) => $table->dropColumn('commentaire'));
        Schema::dropIfExists('publication_type_etablissement');
        Schema::dropIfExists('publications');
        Schema::dropIfExists('paiements_cotisations');
        Schema::dropIfExists('cotisations');
        Schema::table('etablissements', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organisation_patronale_id');
            $table->dropColumn([
                'nature_entite', 'rccm', 'id_nat', 'cnss', 'inpp', 'nif',
                'autorisation_path', 'affiliation_statut',
            ]);
        });
        Schema::dropIfExists('organisations_patronales');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_approved'));
    }
};
