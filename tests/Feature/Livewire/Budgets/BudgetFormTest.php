<?php

namespace Tests\Feature\Livewire\Budgets;

use App\Livewire\Budgets\BudgetForm;
use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BudgetFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_budget_with_category_limits(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(BudgetForm::class)
            ->set('name', 'ميزانيتي')
            ->set('total_amount', '2000')
            ->set('categories', [
                ['category_id' => (string) $category->id, 'limit_amount' => '500', 'alert_percentage' => '80'],
            ])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('budgets', [
            'user_id' => $user->id,
            'name' => 'ميزانيتي',
        ]);
        $this->assertDatabaseHas('budget_categories', [
            'category_id' => $category->id,
            'limit_amount' => 500,
        ]);
    }

    public function test_duplicate_category_ids_are_rejected(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(BudgetForm::class)
            ->set('name', 'ميزانيتي')
            ->set('total_amount', '2000')
            ->set('categories', [
                ['category_id' => (string) $category->id, 'limit_amount' => '500', 'alert_percentage' => '80'],
                ['category_id' => (string) $category->id, 'limit_amount' => '300', 'alert_percentage' => '80'],
            ])
            ->call('save')
            ->assertHasErrors(['categories']);

        $this->assertDatabaseCount('budgets', 0);
    }

    public function test_required_fields_are_validated(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(BudgetForm::class)
            ->set('name', '')
            ->set('total_amount', '')
            ->call('save')
            ->assertHasErrors(['name', 'total_amount']);
    }

    public function test_user_can_edit_their_own_budget(): void
    {
        $user = User::factory()->create();
        $budget = Budget::factory()->for($user)->create(['name' => 'قديمة']);

        Livewire::actingAs($user)
            ->test(BudgetForm::class, ['budgetId' => $budget->id])
            ->assertSet('name', 'قديمة')
            ->set('name', 'محدثة')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('محدثة', $budget->fresh()->name);
    }

    public function test_user_cannot_edit_another_users_budget(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $budget = Budget::factory()->for($other)->create();

        Livewire::actingAs($user)
            ->test(BudgetForm::class, ['budgetId' => $budget->id])
            ->assertForbidden();
    }

    public function test_guest_cannot_access_the_create_form(): void
    {
        $this->get(route('budgets.create'))->assertRedirect(route('login'));
    }
}
