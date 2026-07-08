<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class ImageService
{
    public function __construct(
        private readonly GeocodingService $geocoding,
    ) {}

    /**
     * Crée une œuvre : photo (stockée en base, colonne LONGBLOB partagée avec
     * WordPress), tags et géolocalisation automatique de l'adresse saisie.
     *
     * @param  array<string, mixed>  $data  données validées
     */
    public function create(array $data, ?UploadedFile $photo): Image
    {
        return DB::transaction(function () use ($data, $photo) {
            if ($photo) {
                $data['image'] = $photo->getContent();
            }

            $image = Image::create($data);

            $image->tags()->sync($data['tags'] ?? []);

            $this->syncLocation($image, $data['adresse'] ?? null);

            return $image;
        });
    }

    /**
     * Met à jour une œuvre. L'adresse n'est géocodée que si elle est saisie
     * (elle n'est pas stockée en base : seules les coordonnées le sont).
     *
     * @param  array<string, mixed>  $data  données validées
     */
    public function update(Image $image, array $data, ?UploadedFile $photo): Image
    {
        return DB::transaction(function () use ($image, $data, $photo) {
            if ($photo) {
                $data['image'] = $photo->getContent();
            }

            $image->update($data);

            $image->tags()->sync($data['tags'] ?? []);

            $this->syncLocation($image, $data['adresse'] ?? null);

            return $image;
        });
    }

    public function delete(Image $image): void
    {
        // La photo (blob), la localisation et les liaisons tags suivent la
        // suppression de la ligne (contraintes ON DELETE CASCADE).
        $image->delete();
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
