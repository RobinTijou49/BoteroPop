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
        if (Schema::hasTable('bp_inscription')) {
            return;
        }

        Schema::create('bp_inscription', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nom');
            $table->string('prenom');
            $table->integer('id_event')->index();

            $table->foreign('id_event')->references('id')->on('bp_event')->cascadeOnDelete()->cascadeOnUpdate();
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
