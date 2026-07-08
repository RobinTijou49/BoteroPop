<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Reproduit la table existante de la base WordPress.
     * Ne fait rien si la table existe déjà (base partagée avec le site).
     */
    public function up(): void
    {
        if (Schema::hasTable('bp_image')) {
            return;
        }

        Schema::create('bp_image', function (Blueprint $table) {
            $table->integer('id', true);
            $table->binary('image'); // LONGBLOB : la photo est stockée en base
            $table->string('nom');
            $table->string('description', 256);
            $table->decimal('prix', 10, 2)->nullable();
            $table->string('shopify_variant_id', 50)->nullable();
        });

        // Aligne le type sur le LONGBLOB de la base WordPress (MySQL/MariaDB uniquement).
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            Schema::getConnection()->statement('ALTER TABLE bp_image MODIFY image LONGBLOB NOT NULL');
        }
    }

    /**
     * Table partagée avec WordPress : ne jamais la supprimer au rollback.
     */
    public function down(): void
    {
        //
    }
};
