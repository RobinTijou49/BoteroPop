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
        if (Schema::hasTable('bp_tags')) {
            return;
        }

        Schema::create('bp_tags', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nom');
        });
    }

    /**
     * Table partagée avec WordPress : ne jamais la supprimer au rollback.
     */
    public function down(): void
    {
        //
    }
};
