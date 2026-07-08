<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Fonctionnalité "réservations" retirée du back office (non utilisée).
     * Table propre au back office, jamais partagée avec WordPress : suppression sans risque.
     */
    public function up(): void
    {
        Schema::dropIfExists('bp_reservation');
    }

    public function down(): void
    {
        Schema::create('bp_reservation', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('image_id')->index();
            $table->string('customer_name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('status')->default('en_attente');
            $table->timestamp('reserved_at')->useCurrent();

            $table->foreign('image_id')->references('id')->on('bp_image')->cascadeOnDelete();
        });
    }
};
