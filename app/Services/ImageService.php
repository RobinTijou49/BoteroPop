<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ImageService
{
    public function __construct(
        private readonly GeocodingService $geocoding,
        private readonly WooCommerceService $woocommerce,
    ) {}

    /**
     * Crée une œuvre : photo (stockée en base, colonne LONGBLOB partagée avec
     * WordPress), tags et géolocalisation automatique de l'adresse saisie.
     * Si un prix est renseigné, le produit WooCommerce correspondant est
     * créé automatiquement.
     *
     * @param  array<string, mixed>  $data  données validées
     */
    public function create(array $data, ?UploadedFile $photo): Image
    {
        $image = DB::transaction(function () use ($data, $photo) {
            if ($photo) {
                $data['image'] = $photo->getContent();
            }

            $image = Image::create($data);

            $image->tags()->sync($data['tags'] ?? []);

            $this->syncLocation($image, $data['adresse'] ?? null);

            return $image;
        });

        $this->syncWooCommerceProduct($image);

        return $image;
    }

    /**
     * Met à jour une œuvre. L'adresse n'est géocodée que si elle est saisie
     * (elle n'est pas stockée en base : seules les coordonnées le sont).
     * Le produit WooCommerce lié est mis à jour (ou créé s'il n'existait
     * pas encore et qu'un prix est désormais renseigné).
     *
     * @param  array<string, mixed>  $data  données validées
     */
    public function update(Image $image, array $data, ?UploadedFile $photo): Image
    {
        $image = DB::transaction(function () use ($image, $data, $photo) {
            if ($photo) {
                $data['image'] = $photo->getContent();
            }

            $image->update($data);

            $image->tags()->sync($data['tags'] ?? []);

            $this->syncLocation($image, $data['adresse'] ?? null);

            return $image;
        });

        $this->syncWooCommerceProduct($image, refreshImage: $photo !== null);

        return $image;
    }

    public function delete(Image $image): void
    {
        if ($image->woocommerce_product_id) {
            $this->woocommerce->deleteProduct($image->woocommerce_product_id);
        }

        // La photo (blob), la localisation et les liaisons tags suivent la
        // suppression de la ligne (contraintes ON DELETE CASCADE).
        $image->delete();
    }

    /**
     * Crée ou met à jour le produit WooCommerce d'une œuvre tarifée, en
     * dehors de la transaction d'écriture (appel HTTP externe) et sans
     * jamais faire échouer l'enregistrement de l'œuvre.
     */
    private function syncWooCommerceProduct(Image $image, bool $refreshImage = true): void
    {
        if ((float) $image->prix <= 0) {
            return;
        }

        if ($image->woocommerce_product_id) {
            $this->woocommerce->updateProduct($image, $refreshImage);

            return;
        }

        $product = $this->woocommerce->createProduct($image);

        if ($product) {
            $image->update([
                'woocommerce_product_id' => $product['id'],
                'woocommerce_sku' => $product['sku'],
            ]);
        }
    }

    /**
     * Géocode l'adresse via Nominatim et enregistre les coordonnées dans
     * bp_image_location. Sans résultat, les coordonnées existantes sont
     * conservées (lat/lon sont NOT NULL dans la base WordPress).
     */
    private function syncLocation(Image $image, ?string $adresse): void
    {
        if (! $adresse) {
            return;
        }

        $coordinates = $this->geocoding->geocode($adresse);

        if ($coordinates) {
            $image->location()->updateOrCreate([], $coordinates);
        }
    }
}
