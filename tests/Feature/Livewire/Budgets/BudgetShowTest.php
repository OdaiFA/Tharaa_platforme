<?php

namespace Tests\Feature\Livewire\Budgets;

use App\Livewire\Budgets\BudgetShow;
use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BudgetShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_their_budget(): void
    {
        $user = User::factory()->create();
        $budget = Budget::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(BudgetShow::class, ['budget' => $budget])
            ->assertSee($budget->name);
    }

    public function test_user_cannot_view_another_users_budget(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $budget = Budget::factory()->for($other)->create();

        Livewire::actingAs($user)
            ->test(BudgetShow::class, ['budget' => $budget])
            ->assertForbidden();
    }

    public function test_owner_can_delete_the_budget_after_confirmation(): void
    {
        $user = User::factory()->create();
        $budget = Budget::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(BudgetShow::class, ['budget' => $budget])
            ->call('confirmDelete')
            ->call('delete')
            ->assertRedirect(route('budgets.index'));

        $this->assertSoftDeleted($budget);
    }
}
