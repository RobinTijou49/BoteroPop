<?php

namespace App\Services;

use App\Models\Image;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WooCommerceService
{
    /**
     * Crée le produit WooCommerce correspondant à une œuvre tarifée
     * (produit simple, quantité 1, vente d'une pièce originale).
     *
     * Non bloquant : une erreur est journalisée mais jamais levée, la
     * création de l'œuvre ne doit pas échouer à cause de WooCommerce.
     *
     * @return array{id: int, sku: ?string}|null null si WooCommerce n'est pas configuré ou injoignable
     */
    public function createProduct(Image $image): ?array
    {
        if (! $this->isConfigured()) {
            Log::warning('WooCommerce non configuré : produit non créé pour l\'œuvre', ['image_id' => $image->id]);

            return null;
        }

        $mediaId = $this->uploadImage($image);

        try {
            $response = $this->client()->post('/wp-json/wc/v3/products', [
                'name' => $image->nom,
                'description' => $image->description,
                'type' => 'simple',
                'status' => 'publish',
                'regular_price' => (string) $image->prix,
                'manage_stock' => true,
                'stock_quantity' => 1,
                'virtual' => false,
                'downloadable' => false,
                // On référence l'ID du média déjà uploadé dans WordPress plutôt que
                // son URL, pour éviter que WooCommerce ne la re-télécharge lui-même.
                'images' => $mediaId ? [['id' => $mediaId]] : [],
            ]);
        } catch (ConnectionException $e) {
            Log::warning('WooCommerce injoignable lors de la création du produit', [
                'image_id' => $image->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        if ($response->failed()) {
            Log::warning('Échec de la création du produit WooCommerce', [
                'image_id' => $image->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        return [
            'id' => (int) $response->json('id'),
            'sku' => $response->json('sku') ?: null,
        ];
    }

    /**
     * Met à jour le produit WooCommerce lié à une œuvre (nom, description,
     * prix et, si une nouvelle photo a été envoyée, l'image du produit).
     * Non bloquant, comme createProduct().
     */
    public function updateProduct(Image $image, bool $refreshImage): void
    {
        if (! $image->woocommerce_product_id) {
            return;
        }

        if (! $this->isConfigured()) {
            Log::warning('WooCommerce non configuré : produit non mis à jour pour l\'œuvre', ['image_id' => $image->id]);

            return;
        }

        $payload = [
            'name' => $image->nom,
            'description' => $image->description,
            'regular_price' => (string) $image->prix,
        ];

        if ($refreshImage) {
            $mediaId = $this->uploadImage($image);

            if ($mediaId) {
                $payload['images'] = [['id' => $mediaId]];
            }
        }

        try {
            $response = $this->client()->put('/wp-json/wc/v3/products/'.$image->woocommerce_product_id, $payload);
        } catch (ConnectionException $e) {
            Log::warning('WooCommerce injoignable lors de la mise à jour du produit', [
                'image_id' => $image->id,
                'product_id' => $image->woocommerce_product_id,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        if ($response->failed()) {
            Log::warning('Échec de la mise à jour du produit WooCommerce', [
                'image_id' => $image->id,
                'product_id' => $image->woocommerce_product_id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    /**
     * Supprime définitivement le produit WooCommerce lié à une œuvre
     * supprimée. Non bloquant : la suppression de l'œuvre n'échoue jamais
     * à cause de WooCommerce.
     */
    public function deleteProduct(int $productId): void
    {
        if (! $this->isConfigured()) {
            Log::warning('WooCommerce non configuré : produit non supprimé', ['product_id' => $productId]);

            return;
        }

        try {
            $response = $this->client()->delete('/wp-json/wc/v3/products/'.$productId, ['force' => true]);
        } catch (ConnectionException $e) {
            Log::warning('WooCommerce injoignable lors de la suppression du produit', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        if ($response->failed()) {
            Log::warning('Échec de la suppression du produit WooCommerce', [
                'product_id' => $productId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    private function isConfigured(): bool
    {
        return (bool) (config('services.woocommerce.url') && config('services.woocommerce.consumer_key') && config('services.woocommerce.consumer_secret'));
    }

    /**
     * Envoie la photo de l'œuvre dans la médiathèque WordPress et retourne
     * l'ID du média créé, utilisé comme image du produit WooCommerce.
     */
    private function uploadImage(Image $image): ?int
    {
        $content = $image->getRawOriginal('image');

        if (! $content) {
            return null;
        }

        if (! config('services.woocommerce.wp_username') || ! config('services.woocommerce.wp_app_password')) {
            return null;
        }

        $mime = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $content) ?: 'image/jpeg';
        $extension = match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };

        try {
            $response = Http::withBasicAuth(
                config('services.woocommerce.wp_username'),
                config('services.woocommerce.wp_app_password'),
            )
                ->timeout(15)
                // Le site est encore accédé par IP avec un certificat auto-signé.
                ->withOptions(['verify' => false])
                ->withHeaders([
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'attachment; filename="oeuvre-'.$image->id.'.'.$extension.'"',
                ])
                ->withBody($content, $mime)
                ->post(rtrim(config('services.woocommerce.url'), '/').'/wp-json/wp/v2/media');
        } catch (ConnectionException $e) {
            Log::warning('Envoi de la photo à la médiathèque WordPress impossible', [
                'image_id' => $image->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        if ($response->failed()) {
            Log::warning('Échec de l\'envoi de la photo à la médiathèque WordPress', [
                'image_id' => $image->id,
                'status' => $response->status(),
            ]);

            return null;
        }

        return (int) $response->json('id');
    }

    private function client()
    {
        return Http::withBasicAuth(
            config('services.woocommerce.consumer_key'),
            config('services.woocommerce.consumer_secret'),
        )
            ->timeout(15)
            // Le site est encore accédé par IP avec un certificat auto-signé.
            ->withOptions(['verify' => false])
            ->baseUrl(rtrim(config('services.woocommerce.url'), '/'));
    }
}
