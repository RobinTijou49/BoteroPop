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
}
