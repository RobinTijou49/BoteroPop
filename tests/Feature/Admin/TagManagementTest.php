<?php

namespace Tests\Feature\Admin;

use App\Models\Image;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_a_tag_can_be_created(): void
    {
        $this->actingAs($this->admin)->post('/admin/tags', [
            'nom' => 'Art Contemporain',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('bp_tags', ['nom' => 'Art Contemporain']);
    }

    public function test_tag_names_must_be_unique(): void
    {
        Tag::factory()->create(['nom' => 'Peinture']);

        $this->actingAs($this->admin)->post('/admin/tags', [
            'nom' => 'Peinture',
        ])->assertSessionHasErrors('nom');
    }

    public function test_a_tag_can_be_renamed(): void
    {
        $tag = Tag::factory()->create(['nom' => 'Ancien nom']);

        $this->actingAs($this->admin)->put("/admin/tags/{$tag->id}", [
            'nom' => 'Nouveau nom',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('bp_tags', [
            'id' => $tag->id,
            'nom' => 'Nouveau nom',
        ]);
    }

    public function test_deleting_a_tag_detaches_it_from_images(): void
    {
        $tag = Tag::factory()->create();
        $image = Image::factory()->create();
        $image->tags()->attach($tag);

        $this->actingAs($this->admin)->delete("/admin/tags/{$tag->id}");

        $this->assertDatabaseMissing('bp_tags', ['id' => $tag->id]);
        $this->assertDatabaseMissing('bp_image_tags', ['tag_id' => $tag->id]);
        $this->assertDatabaseHas('bp_image', ['id' => $image->id]);
    }
}
