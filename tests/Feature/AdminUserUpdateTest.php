<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_a_regular_users_allowed_fields(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['name' => 'قديم', 'financial_level' => 'beginner']);

        $this->actingAs($admin)
            ->from(route('admin.users.show', $target))
            ->put(route('admin.users.update', $target), [
                'name' => 'اسم جديد',
                'email' => $target->email,
                'role' => 'user',
                'financial_level' => 'advanced',
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.users.show', $target));

        $target->refresh();
        $this->assertSame('اسم جديد', $target->name);
        $this->assertSame('advanced', $target->financial_level);
    }

    public function test_update_route_accepts_the_forms_spoofed_put_method(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['name' => 'قديم']);

        $response = $this->actingAs($admin)
            ->from(route('admin.users.show', $target))
            ->post(route('admin.users.update', $target), [
                '_method' => 'PUT',
                'name' => 'محدث عبر النموذج',
                'email' => $target->email,
                'role' => 'user',
                'financial_level' => $target->financial_level,
                'is_active' => '1',
            ]);

        $response->assertRedirect(route('admin.users.show', $target));
        $this->assertSame('محدث عبر النموذج', $target->fresh()->name);
    }

    public function test_regular_user_cannot_update_another_user_through_admin_route(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create(['name' => 'محمي']);

        $this->actingAs($user)
            ->put(route('admin.users.update', $target), ['name' => 'مخترق'])
            ->assertForbidden();

        $this->assertSame('محمي', $target->fresh()->name);
    }

    public function test_guest_cannot_access_the_update_route(): void
    {
        $target = User::factory()->create();

        $this->put(route('admin.users.update', $target), ['name' => 'x'])
            ->assertRedirect(route('login'));
    }

    public function test_duplicate_email_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->create(['email' => 'taken@example.com']);
        $target = User::factory()->create(['email' => 'target@example.com']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), ['email' => 'taken@example.com'])
            ->assertSessionHasErrors('email');

        $this->assertSame('target@example.com', $target->fresh()->email);
    }

    public function test_targets_own_unchanged_email_is_accepted(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['email' => 'unchanged@example.com']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name' => $target->name,
                'email' => 'unchanged@example.com',
            ])
            ->assertSessionHasNoErrors();
    }

    public function test_invalid_role_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), ['role' => 'super-admin'])
            ->assertSessionHasErrors('role');

        $this->assertSame('user', $target->fresh()->role);
    }

    public function test_deactivating_a_user_persists_and_blocks_login(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name' => $target->name,
                'is_active' => '0',
            ])
            ->assertSessionHasNoErrors();

        $target->refresh();
        $this->assertFalse($target->is_active);
        $this->assertNull($target->deleted_at, 'Deactivation must not soft-delete the account.');

        auth()->logout();

        $this->post(route('login.attempt'), [
            'email' => $target->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_reactivating_a_user_persists_and_restores_login(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['is_active' => false]);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $target), [
                'name' => $target->name,
                'is_active' => '1',
            ]);

        $this->assertTrue($target->fresh()->is_active);

        auth()->logout();

        $this->post(route('login.attempt'), [
            'email' => $target->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($target->fresh());
    }

    public function test_active_admin_can_still_log_in(): void
    {
        $admin = User::factory()->admin()->create();

        $this->post(route('login.attempt'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_cannot_deactivate_their_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('admin.users.update', $admin), [
                'name' => $admin->name,
                'is_active' => '0',
            ])
            ->assertForbidden();

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_fields_not_included_in_the_request_are_not_overwritten(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create([
            'currency' => 'SAR',
            'financial_level' => 'intermediate',
        ]);

        $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'name' => 'اسم فقط',
        ]);

        $target->refresh();
        $this->assertSame('اسم فقط', $target->name);
        $this->assertSame('SAR', $target->currency);
        $this->assertSame('intermediate', $target->financial_level);
    }

    public function test_sensitive_fields_cannot_be_mass_assigned_through_this_endpoint(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();
        $originalPassword = $target->password;

        $this->actingAs($admin)->put(route('admin.users.update', $target), [
            'name' => $target->name,
            'password' => 'hacked-password',
            'remember_token' => 'attacker-token',
        ]);

        $target->refresh();
        $this->assertSame($originalPassword, $target->password);
        $this->assertNotSame('attacker-token', $target->remember_token);
    }

    public function test_admin_can_update_another_admins_profile(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create(['name' => 'مدير آخر']);

        $this->actingAs($admin)
            ->put(route('admin.users.update', $otherAdmin), [
                'name' => 'مدير محدث',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('مدير محدث', $otherAdmin->fresh()->name);
    }
}
