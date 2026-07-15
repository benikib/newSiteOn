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
        // =============================================
        // 1. Ajout du champ TVA à la table products
        // =============================================
        if (Schema::hasTable('products')) {
            if (!Schema::hasColumn('products', 'tva_rate')) {
                Schema::table('products', function (Blueprint $table) {
                    // La colonne 'status' existe déjà, on ajoute après
                    $table->decimal('tva_rate', 5, 2)
                          ->default(20)
                          ->after('status')
                          ->comment('Taux de TVA applicable au produit');
                });
                
                echo "✅ Colonne 'tva_rate' ajoutée à la table 'products'\n";
            } else {
                echo "ℹ️ Colonne 'tva_rate' existe déjà dans 'products'\n";
            }
        }

        // =============================================
        // 2. Ajout du champ type à la table movements
        // =============================================
        if (Schema::hasTable('movements')) {
            if (!Schema::hasColumn('movements', 'type')) {
                Schema::table('movements', function (Blueprint $table) {
                    $table->enum('type', [
                        'in',                    // Entrée de stock
                        'out',                   // Sortie de stock
                        'adjust_positive',       // Ajustement positif
                        'adjust_negative',       // Ajustement négatif
                        'inventory_in',          // Inventaire entrée
                        'inventory_out',         // Inventaire sortie
                        'stock_in_create',       // Création de stock
                        'stock_in_update'        // Mise à jour de stock
                    ])->default('in')
                      ->after('product_id')
                      ->comment('Type de mouvement de stock');
                });
                
                echo "✅ Colonne 'type' ajoutée à la table 'movements'\n";
            } else {
                echo "ℹ️ Colonne 'type' existe déjà dans 'movements'\n";
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // =============================================
        // 1. Suppression du champ TVA
        // =============================================
        if (Schema::hasTable('products')) {
            if (Schema::hasColumn('products', 'tva_rate')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->dropColumn('tva_rate');
                });
                
                echo "✅ Colonne 'tva_rate' supprimée de 'products'\n";
            }
        }

        // =============================================
        // 2. Suppression du champ type
        // =============================================
        if (Schema::hasTable('movements')) {
            if (Schema::hasColumn('movements', 'type')) {
                Schema::table('movements', function (Blueprint $table) {
                    $table->dropColumn('type');
                });
                
                echo "✅ Colonne 'type' supprimée de 'movements'\n";
            }
        }
    }
};