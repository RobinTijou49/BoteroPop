<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute les colonnes de liaison avec le produit WooCommerce créé
     * automatiquement lorsqu'une œuvre est tarifée.
     */
    public function up(): void
    {
        Schema::table('bp_image', function (Blueprint $table) {
            if (! Schema::hasColumn('bp_image', 'woocommerce_product_id')) {
                $table->unsignedBigInteger('woocommerce_product_id')->nullable()->after('shopify_variant_id');
            }

            if (! Schema::hasColumn('bp_image', 'woocommerce_sku')) {
                $table->string('woocommerce_sku', 100)->nullable()->after('woocommerce_product_id');
            }
        });
    }

    /**
     * Table partagée avec WordPress : ne jamais supprimer de colonnes au rollback.
     */
    public function down(): void
    {
        //
    }
};
