<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
    }

    public function test_an_admin_can_create_a_user(): void
    {
        $this->actingAs($this->admin)->post('/admin/utilisateurs', [
            'name' => 'Nouvel Admin',
            'email' => 'nouveau@monsite.fr',
            'password' => 'MotDePasse123!',
            'password_confirmation' => 'MotDePasse123!',
            'is_active' => '1',
        ])->assertRedirect(route('admin.utilisateurs.index', absolute: false));

        $user = User::firstWhere('email', 'nouveau@monsite.fr');

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('MotDePasse123!', $user->password));
        $this->assertTrue($user->is_active);
    }

    public function test_an_admin_can_reset_another_users_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->patch("/admin/utilisateurs/{$user->id}/mot-de-passe", [
                'password' => 'NouveauPass123!',
                'password_confirmation' => 'NouveauPass123!',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('NouveauPass123!', $user->refresh()->password));
    }

    public function test_an_admin_can_deactivate_another_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->patch("/admin/utilisateurs/{$user->id}/activation")
            ->assertSessionHasNoErrors();

        $this->assertFalse($user->refresh()->is_active);
    }

    public function test_an_admin_cannot_deactivate_their_own_account(): void
    {
        $this->actingAs($this->admin)
            ->patch("/admin/utilisateurs/{$this->admin->id}/activation")
            ->assertSessionHasErrors('user');

        $this->assertTrue($this->admin->refresh()->is_active);
    }

    public function test_an_admin_can_delete_another_user(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin)
            ->delete("/admin/utilisateurs/{$user->id}")
            ->assertSessionHasNoErrors();

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_an_admin_cannot_delete_their_own_account(): void
    {
        $this->actingAs($this->admin)
            ->delete("/admin/utilisateurs/{$this->admin->id}")
            ->assertSessionHasErrors('user');

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }
}
