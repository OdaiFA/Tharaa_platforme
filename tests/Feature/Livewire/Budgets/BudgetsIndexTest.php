<?php

namespace Tests\Feature\Livewire\Budgets;

use App\Livewire\Budgets\BudgetsIndex;
use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BudgetsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_render_budgets_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('budgets.index'))
            ->assertOk()
            ->assertSeeLivewire(BudgetsIndex::class);
    }

    public function test_guest_cannot_access_budgets_index(): void
    {
        $this->get(route('budgets.index'))->assertRedirect(route('login'));
    }

    public function test_user_only_sees_own_active_budgets(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Budget::factory()->for($user)->create(['name' => 'ميزانيتي']);
        Budget::factory()->for($other)->create(['name' => 'ميزانية أخرى']);
        Budget::factory()->for($user)->create(['name' => 'ميزانية غير نشطة', 'is_active' => false]);

        Livewire::actingAs($user)
            ->test(BudgetsIndex::class)
            ->assertSee('ميزانيتي')
            ->assertDontSee('ميزانية أخرى');
    }
}
