<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\Inscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventAndInscriptionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_an_event_can_be_created(): void
    {
        $this->actingAs($this->admin)->post('/admin/evenements', [
            'nom' => 'Vernissage de printemps',
            'description' => 'Ouverture de la nouvelle collection.',
            'lieu' => 'Galerie BoteroPop, Paris',
            'date' => '2026-09-15',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('bp_event', ['nom' => 'Vernissage de printemps']);
    }

    public function test_event_requires_all_mandatory_fields(): void
    {
        $this->actingAs($this->admin)->post('/admin/evenements', [
            'nom' => '',
            'description' => '',
            'lieu' => '',
            'date' => '',
        ])->assertSessionHasErrors(['nom', 'description', 'lieu', 'date']);
    }

    public function test_the_event_detail_page_shows_its_inscription_count(): void
    {
        $event = Event::factory()->create();
        Inscription::factory(3)->create(['id_event' => $event->id]);

        $response = $this->actingAs($this->admin)->get("/admin/evenements/{$event->id}");

        $response->assertOk();
        $response->assertSee('3');
        $response->assertSee($event->nom);
    }

    public function test_inscriptions_can_be_filtered_by_event_and_searched_by_name(): void
    {
        $eventA = Event::factory()->create();
        $eventB = Event::factory()->create();

        Inscription::factory()->create(['id_event' => $eventA->id, 'nom' => 'Dupont', 'prenom' => 'Alice']);
        Inscription::factory()->create(['id_event' => $eventB->id, 'nom' => 'Martin', 'prenom' => 'Bob']);

        $byEvent = $this->actingAs($this->admin)->get("/admin/inscriptions?event={$eventA->id}");
        $byEvent->assertOk();
        $byEvent->assertSee('Dupont');
        $byEvent->assertDontSee('Martin');

        $byName = $this->actingAs($this->admin)->get('/admin/inscriptions?q=bob');
        $byName->assertOk();
        $byName->assertSee('Martin');
        $byName->assertDontSee('Dupont');
    }

    public function test_inscriptions_can_be_sorted_by_registration_order(): void
    {
        $event = Event::factory()->create();
        Inscription::factory()->create(['id_event' => $event->id, 'nom' => 'Premier', 'prenom' => 'A']);
        Inscription::factory()->create(['id_event' => $event->id, 'nom' => 'Second', 'prenom' => 'B']);

        $response = $this->actingAs($this->admin)->get('/admin/inscriptions?sort=asc');

        $response->assertOk();
        $response->assertSeeInOrder(['Premier', 'Second']);

        $response = $this->actingAs($this->admin)->get('/admin/inscriptions?sort=desc');

        $response->assertSeeInOrder(['Second', 'Premier']);
    }

    public function test_deleting_an_event_cascades_to_its_inscriptions(): void
    {
        $event = Event::factory()->create();
        Inscription::factory(2)->create(['id_event' => $event->id]);

        $this->actingAs($this->admin)->delete("/admin/evenements/{$event->id}");

        $this->assertDatabaseMissing('bp_event', ['id' => $event->id]);
        $this->assertDatabaseCount('bp_inscription', 0);
    }
}
