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
        if (Schema::hasTable('bp_image_tags')) {
            return;
        }

        Schema::create('bp_image_tags', function (Blueprint $table) {
            $table->integer('image_id');
            $table->integer('tag_id')->index();

            $table->primary(['image_id', 'tag_id']);
            $table->foreign('image_id')->references('id')->on('bp_image')->cascadeOnDelete();
            $table->foreign('tag_id')->references('id')->on('bp_tags')->cascadeOnDelete();
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
