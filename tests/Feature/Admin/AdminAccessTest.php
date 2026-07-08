<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Toute tentative d'accès non authentifié redirige vers /login.
     */
    public function test_guests_are_redirected_to_login_on_every_admin_route(): void
    {
        $routes = [
            '/admin',
            '/admin/oeuvres',
            '/admin/oeuvres/create',
            '/admin/evenements',
            '/admin/inscriptions',
            '/admin/reservations',
            '/admin/tags',
            '/admin/utilisateurs',
            '/admin/parametres',
        ];

        foreach ($routes as $route) {
            $this->get($route)->assertRedirect('/login');
        }
    }

    public function test_authenticated_users_can_access_the_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Déconnexion');
    }

    public function test_admin_pages_render_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        foreach ([
            '/admin/oeuvres',
            '/admin/evenements',
            '/admin/inscriptions',
            '/admin/reservations',
            '/admin/tags',
            '/admin/utilisateurs',
            '/admin/parametres',
        ] as $route) {
            $this->actingAs($user)->get($route)->assertOk();
        }
    }
}
