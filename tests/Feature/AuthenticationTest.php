<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_sign_in(): void
    {
        $user = User::factory()->create(['password' => 'correct-password']);

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'correct-password',
        ]);

        $response->assertRedirect(route('copil.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_disabled_user_cannot_sign_in(): void
    {
        $user = User::factory()->create(['active' => false, 'password' => 'correct-password']);

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'correct-password'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_dashboard_shows_setup_screen_when_no_period_exists(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('copil.index'))
            ->assertOk()
            ->assertSee('Le premier mois n’est pas encore créé.')
            ->assertSee('Créer la première période');
    }
}
