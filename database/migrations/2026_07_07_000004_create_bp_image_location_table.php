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
        if (Schema::hasTable('bp_image_location')) {
            return;
        }

        Schema::create('bp_image_location', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('image_id')->index();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);

            $table->foreign('image_id')->references('id')->on('bp_image')->cascadeOnDelete();
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
