<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\Users\UserShow;
use App\Models\Account;
use App\Models\Enrollment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_user_show_for_a_regular_user(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['name' => 'عبدالله الشمري']);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $target))
            ->assertOk()
            ->assertSeeLivewire(UserShow::class)
            ->assertSee('عبدالله الشمري');
    }

    public function test_admin_can_render_user_show_for_another_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create(['name' => 'مدير آخر']);

        $this->actingAs($admin)
            ->get(route('admin.users.show', $otherAdmin))
            ->assertOk()
            ->assertSee('مدير آخر');
    }

    public function test_regular_user_cannot_access_user_show(): void
    {
        $user = User::factory()->create();
        $target = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.users.show', $target))
            ->assertForbidden();
    }

    public function test_user_show_does_not_expose_sensitive_fields(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        $response = $this->actingAs($admin)->get(route('admin.users.show', $target));

        $response->assertOk();
        $response->assertDontSee($target->password, false);
        $response->assertDontSee($target->remember_token, false);
    }

    public function test_totals_reflect_the_users_actual_data(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();
        $account = Account::factory()->for($target)->create(['balance' => 500]);
        Account::factory()->for($target)->create(['balance' => 250]);
        Transaction::factory()->for($target)->for($account)->count(3)->create();
        Enrollment::factory()->for($target)->count(2)->create();

        Livewire::actingAs($admin)
            ->test(UserShow::class, ['user' => $target])
            ->assertSee('750.00')
            ->assertSeeText('3')
            ->assertSeeText('2');
    }
}
