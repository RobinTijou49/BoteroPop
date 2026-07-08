<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodingService
{
    /**
     * Convertit une adresse postale en coordonnées GPS via l'API
     * OpenStreetMap Nominatim.
     *
     * @return array{latitude: float, longitude: float}|null null si l'adresse est introuvable ou l'API indisponible
     */
    public function geocode(string $address): ?array
    {
        $address = trim($address);

        if ($address === '') {
            return null;
        }

        try {
            $response = Http::withHeaders([
                // La politique d'utilisation de Nominatim exige un User-Agent identifiant l'application.
                'User-Agent' => config('services.nominatim.user_agent'),
            ])
                ->timeout(10)
                ->get(config('services.nominatim.url').'/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                ]);
        } catch (ConnectionException $e) {
            Log::warning('Géocodage Nominatim injoignable', ['address' => $address, 'error' => $e->getMessage()]);

            return null;
        }

        if ($response->failed()) {
            Log::warning('Géocodage Nominatim en échec', ['address' => $address, 'status' => $response->status()]);

            return null;
        }

        $result = $response->json()[0] ?? null;

        if (! $result || ! isset($result['lat'], $result['lon'])) {
            return null;
        }

        return [
            'latitude' => (float) $result['lat'],
            'longitude' => (float) $result['lon'],
        ];
    }
}
