<?php

namespace Tests\Feature\Admin;

use App\Models\Image;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ImageManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_an_image_can_be_created_with_photo_tags_and_geocoded_address(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '48.8588897', 'lon' => '2.3200410'],
            ]),
        ]);

        $tags = Tag::factory(2)->create();

        $response = $this->actingAs($this->admin)->post('/admin/oeuvres', [
            'nom' => 'La Joconde Pop',
            'description' => 'Revisite pop art.',
            'prix' => '1250.50',
            'photo' => UploadedFile::fake()->image('oeuvre.jpg', 1200, 900),
            'adresse' => 'Tour Eiffel, Paris',
            'tags' => $tags->pluck('id')->all(),
        ]);

        $image = Image::firstWhere('nom', 'La Joconde Pop');

        $response->assertRedirect(route('admin.oeuvres.show', $image, absolute: false));

        $this->assertNotNull($image);
        // La photo est stockée en base (colonne blob), pas sur le disque.
        $this->assertNotEmpty($image->getRawOriginal('image'));

        $this->assertEqualsCanonicalizing(
            $tags->pluck('id')->all(),
            $image->tags->pluck('id')->all(),
        );

        $this->assertNotNull($image->location);
        $this->assertEqualsWithDelta(48.8588897, (float) $image->location->latitude, 0.0001);
        $this->assertEqualsWithDelta(2.3200410, (float) $image->location->longitude, 0.0001);
    }

    public function test_the_stored_photo_is_served_by_the_photo_route(): void
    {
        Http::fake();

        $image = Image::factory()->create();

        $response = $this->actingAs($this->admin)->get("/admin/oeuvres/{$image->id}/photo");

        $response->assertOk();
        $this->assertStringStartsWith('image/', $response->headers->get('Content-Type'));
    }

    public function test_image_creation_survives_a_geocoding_failure(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([], 503),
        ]);

        $this->actingAs($this->admin)->post('/admin/oeuvres', [
            'nom' => 'Œuvre sans géocodage',
            'description' => 'Test de résilience.',
            'photo' => UploadedFile::fake()->image('oeuvre.png'),
            'adresse' => 'Adresse inconnue',
        ]);

        $image = Image::firstWhere('nom', 'Œuvre sans géocodage');

        $this->assertNotNull($image);
        $this->assertNull($image->location);
    }

    public function test_photo_upload_rejects_invalid_formats_and_oversized_files(): void
    {
        $this->actingAs($this->admin)->post('/admin/oeuvres', [
            'nom' => 'Format invalide',
            'description' => 'Test.',
            'photo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
        ])->assertSessionHasErrors('photo');

        $this->actingAs($this->admin)->post('/admin/oeuvres', [
            'nom' => 'Trop volumineux',
            'description' => 'Test.',
            'photo' => UploadedFile::fake()->image('grande.jpg')->size(11 * 1024),
        ])->assertSessionHasErrors('photo');

        $this->assertDatabaseCount('bp_image', 0);
    }

    public function test_updating_with_an_address_regeocodes_the_image(): void
    {
        Http::fake([
            'nominatim.openstreetmap.org/*' => Http::response([
                ['lat' => '45.7578137', 'lon' => '4.8320114'],
            ]),
        ]);

        $image = Image::factory()->create();
        $image->location()->create(['latitude' => 48.85, 'longitude' => 2.35]);

        $this->actingAs($this->admin)->put("/admin/oeuvres/{$image->id}", [
            'nom' => $image->nom,
            'description' => $image->description,
            'adresse' => 'Place Bellecour, Lyon',
        ])->assertSessionHasNoErrors();

        $image->refresh();

        $this->assertEqualsWithDelta(45.7578137, (float) $image->location->latitude, 0.0001);
        $this->assertEqualsWithDelta(4.8320114, (float) $image->location->longitude, 0.0001);
    }

    public function test_updating_without_an_address_keeps_the_existing_coordinates(): void
    {
        Http::fake();

        $image = Image::factory()->create();
        $image->location()->create(['latitude' => 48.85, 'longitude' => 2.35]);

        $this->actingAs($this->admin)->put("/admin/oeuvres/{$image->id}", [
            'nom' => 'Nom modifié',
            'description' => $image->description,
        ])->assertSessionHasNoErrors();

        $image->refresh();

        $this->assertEqualsWithDelta(48.85, (float) $image->location->latitude, 0.0001);
        Http::assertNothingSent();
    }

    public function test_deleting_an_image_removes_its_location_and_tag_links(): void
    {
        $tag = Tag::factory()->create();
        $image = Image::factory()->create();
        $image->tags()->attach($tag);
        $image->location()->create(['latitude' => 48.85, 'longitude' => 2.35]);

        $this->actingAs($this->admin)
            ->delete("/admin/oeuvres/{$image->id}")
            ->assertRedirect(route('admin.oeuvres.index', absolute: false));

        $this->assertDatabaseMissing('bp_image', ['id' => $image->id]);
        $this->assertDatabaseMissing('bp_image_location', ['image_id' => $image->id]);
        $this->assertDatabaseMissing('bp_image_tags', ['image_id' => $image->id]);
    }

    public function test_images_can_be_searched_by_name(): void
    {
        Image::factory()->create(['nom' => 'Girafe multicolore']);
        Image::factory()->create(['nom' => 'Nature morte']);

        $response = $this->actingAs($this->admin)->get('/admin/oeuvres?q=girafe');

        $response->assertOk();
        $response->assertSee('Girafe multicolore');
        $response->assertDontSee('Nature morte');
    }

    public function test_creating_a_priced_image_creates_a_woocommerce_product(): void
    {
        config([
            'services.woocommerce.url' => 'https://boutique.example.com',
            'services.woocommerce.consumer_key' => 'ck_test',
            'services.woocommerce.consumer_secret' => 'cs_test',
        ]);

        Http::fake([
            'boutique.example.com/wp-json/wp/v2/media' => Http::response(['id' => 55, 'source_url' => 'https://boutique.example.com/media/1.jpg'], 201),
            'boutique.example.com/wp-json/wc/v3/products' => Http::response(['id' => 987, 'sku' => 'OEUVRE-987'], 201),
        ]);

        $this->actingAs($this->admin)->post('/admin/oeuvres', [
            'nom' => 'Toile tarifée',
            'description' => 'Œuvre à vendre.',
            'prix' => '499.00',
            'photo' => UploadedFile::fake()->image('oeuvre.jpg'),
        ]);

        $image = Image::firstWhere('nom', 'Toile tarifée');

        $this->assertNotNull($image);
        $this->assertSame(987, $image->woocommerce_product_id);
        $this->assertSame('OEUVRE-987', $image->woocommerce_sku);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://boutique.example.com/wp-json/wc/v3/products'
                && $request['name'] === 'Toile tarifée'
                && $request['regular_price'] === '499.00'
                && $request['manage_stock'] === true
                && $request['stock_quantity'] === 1;
        });
    }

    public function test_creating_an_image_without_a_price_does_not_call_woocommerce(): void
    {
        config([
            'services.woocommerce.url' => 'https://boutique.example.com',
            'services.woocommerce.consumer_key' => 'ck_test',
            'services.woocommerce.consumer_secret' => 'cs_test',
        ]);

        Http::fake();

        $this->actingAs($this->admin)->post('/admin/oeuvres', [
            'nom' => 'Toile sans prix',
            'description' => 'Œuvre non tarifée.',
            'photo' => UploadedFile::fake()->image('oeuvre.jpg'),
        ]);

        $image = Image::firstWhere('nom', 'Toile sans prix');

        $this->assertNotNull($image);
        $this->assertNull($image->woocommerce_product_id);
        Http::assertNothingSent();
    }

    public function test_image_creation_survives_a_woocommerce_failure(): void
    {
        config([
            'services.woocommerce.url' => 'https://boutique.example.com',
            'services.woocommerce.consumer_key' => 'ck_test',
            'services.woocommerce.consumer_secret' => 'cs_test',
        ]);

        Http::fake([
            'boutique.example.com/*' => Http::response([], 500),
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/oeuvres', [
            'nom' => 'Toile malgré la panne',
            'description' => 'WooCommerce est en panne.',
            'prix' => '150.00',
            'photo' => UploadedFile::fake()->image('oeuvre.jpg'),
        ]);

        $image = Image::firstWhere('nom', 'Toile malgré la panne');

        $response->assertRedirect(route('admin.oeuvres.show', $image, absolute: false));
        $this->assertNotNull($image);
        $this->assertNull($image->woocommerce_product_id);
    }

    public function test_updating_a_linked_product_pushes_the_new_price_to_woocommerce(): void
    {
        config([
            'services.woocommerce.url' => 'https://boutique.example.com',
            'services.woocommerce.consumer_key' => 'ck_test',
            'services.woocommerce.consumer_secret' => 'cs_test',
        ]);

        $image = Image::factory()->create(['prix' => 100, 'woocommerce_product_id' => 987, 'woocommerce_sku' => 'OEUVRE-987']);

        Http::fake([
            'boutique.example.com/wp-json/wc/v3/products/987' => Http::response(['id' => 987], 200),
        ]);

        $this->actingAs($this->admin)->put("/admin/oeuvres/{$image->id}", [
            'nom' => $image->nom,
            'description' => $image->description,
            'prix' => '650.00',
        ])->assertSessionHasNoErrors();

        Http::assertSent(function ($request) {
            return $request->url() === 'https://boutique.example.com/wp-json/wc/v3/products/987'
                && $request->method() === 'PUT'
                && $request['regular_price'] === '650.00';
        });

        // Aucune nouvelle photo envoyée : la médiathèque WordPress n'est pas sollicitée.
        Http::assertNotSent(fn ($request) => str_contains($request->url(), '/wp-json/wp/v2/media'));
    }

    public function test_updating_an_unpriced_image_creates_the_missing_product(): void
    {
        config([
            'services.woocommerce.url' => 'https://boutique.example.com',
            'services.woocommerce.consumer_key' => 'ck_test',
            'services.woocommerce.consumer_secret' => 'cs_test',
        ]);

        $image = Image::factory()->create(['prix' => null, 'woocommerce_product_id' => null]);

        Http::fake([
            'boutique.example.com/wp-json/wp/v2/media' => Http::response(['id' => 55], 201),
            'boutique.example.com/wp-json/wc/v3/products' => Http::response(['id' => 321, 'sku' => 'OEUVRE-321'], 201),
        ]);

        $this->actingAs($this->admin)->put("/admin/oeuvres/{$image->id}", [
            'nom' => $image->nom,
            'description' => $image->description,
            'prix' => '75.00',
        ])->assertSessionHasNoErrors();

        $image->refresh();

        $this->assertSame(321, $image->woocommerce_product_id);
        $this->assertSame('OEUVRE-321', $image->woocommerce_sku);
    }

    public function test_deleting_an_image_deletes_its_woocommerce_product(): void
    {
        config([
            'services.woocommerce.url' => 'https://boutique.example.com',
            'services.woocommerce.consumer_key' => 'ck_test',
            'services.woocommerce.consumer_secret' => 'cs_test',
        ]);

        $image = Image::factory()->create(['prix' => 100, 'woocommerce_product_id' => 987]);

        Http::fake([
            'boutique.example.com/wp-json/wc/v3/products/987' => Http::response(['id' => 987], 200),
        ]);

        $this->actingAs($this->admin)
            ->delete("/admin/oeuvres/{$image->id}")
            ->assertRedirect(route('admin.oeuvres.index', absolute: false));

        $this->assertDatabaseMissing('bp_image', ['id' => $image->id]);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://boutique.example.com/wp-json/wc/v3/products/987'
                && $request->method() === 'DELETE'
                && $request['force'] === true;
        });
    }

    public function test_deleting_an_image_without_a_product_does_not_call_woocommerce(): void
    {
        config([
            'services.woocommerce.url' => 'https://boutique.example.com',
            'services.woocommerce.consumer_key' => 'ck_test',
            'services.woocommerce.consumer_secret' => 'cs_test',
        ]);

        $image = Image::factory()->create(['woocommerce_product_id' => null]);

        Http::fake();

        $this->actingAs($this->admin)->delete("/admin/oeuvres/{$image->id}");

        Http::assertNothingSent();
    }
}
