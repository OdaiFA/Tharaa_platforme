<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\Users\UsersIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UsersIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_users_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSeeLivewire(UsersIndex::class);
    }

    public function test_regular_user_cannot_access_users_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_user_list_renders_expected_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'محمد العتيبي']);

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->assertSee('محمد العتيبي');
    }

    public function test_search_filters_by_name_or_email(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'سارة الدوسري', 'email' => 'sara@example.com']);
        User::factory()->create(['name' => 'خالد القحطاني', 'email' => 'khaled@example.com']);

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->set('search', 'سارة')
            ->assertSee('سارة الدوسري')
            ->assertDontSee('خالد القحطاني');

        Livewire::actingAs($admin)
            ->test(UsersIndex::class)
            ->set('search', 'khaled@example.com')
            ->assertSee('خالد القحطاني')
            ->assertDontSee('سارة الدوسري');
    }

    public function test_pagination_limits_to_fifteen_per_page(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(20)->create();

        $component = Livewire::actingAs($admin)->test(UsersIndex::class);

        $this->assertCount(15, $component->viewData('users'));
    }
}
