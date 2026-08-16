<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    public function test_guest_is_redirected_to_login_from_admin_pages(): void
    {
        $this->get('/admin/users')->assertRedirect(route('login'));
    }

    public function test_register_creates_user_and_logs_in(): void
    {
        $response = $this->post('/register', [
            'name' => 'أحمد العتيبي',
            'email' => 'ahmed@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'date_of_birth' => '2000-01-15',
            'financial_level' => 'beginner',
            'currency' => 'SAR',
        ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['email' => 'ahmed@example.com', 'role' => 'user']);
        $this->assertAuthenticated();

        $user = User::where('email', 'ahmed@example.com')->firstOrFail();
        $this->assertNotNull($user->settings);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->post('/register', [
            'name' => 'مستخدم جديد',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');
    }

    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create(['email' => 'user@example.com']);

        $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_wrong_password_redirects_back_with_errors(): void
    {
        User::factory()->create(['email' => 'user@example.com']);

        $this->post('/login', [
            'email' => 'user@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_ends_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/logout')
            ->assertRedirect(route('landing'));

        $this->assertGuest();
    }
}
