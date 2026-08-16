<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk();
    }

    public function test_admin_can_manage_users(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->get('/admin/users')
            ->assertOk();

        $this->actingAs($admin)
            ->get("/admin/users/{$target->id}")
            ->assertOk();
    }

    public function test_regular_user_is_blocked_from_admin_pages(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/admin/users')
            ->assertForbidden();

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertForbidden();

        $this->actingAs($user)
            ->get('/admin/statistics')
            ->assertForbidden();
    }

    public function test_admin_can_access_all_admin_sections(): void
    {
        $admin = User::factory()->admin()->create();

        $urls = [
            '/admin/users',
            '/admin/courses',
            '/admin/courses/create',
            '/admin/articles',
            '/admin/articles/create',
            '/admin/categories',
            '/admin/article-categories',
            '/admin/age-groups',
            '/admin/statistics',
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }
}
