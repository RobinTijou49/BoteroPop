<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table propre au back office (réservations d'œuvres, affichées sur le
     * dashboard). Créée dans la même base que les tables bp_* de WordPress,
     * en respectant leur convention (préfixe bp_, ids int, pas de timestamps
     * Laravel — la date de réservation est portée par reserved_at).
     */
    public function up(): void
    {
        if (Schema::hasTable('bp_reservation')) {
            return;
        }

        Schema::create('bp_reservation', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('image_id')->index();
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('status')->default('en_attente'); // en_attente, confirmee, annulee
            $table->timestamp('reserved_at')->useCurrent();

            $table->foreign('image_id')->references('id')->on('bp_image')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bp_reservation');
    }
};
