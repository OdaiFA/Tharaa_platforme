<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\Categories\CategoriesIndex;
use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoriesIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_render_the_categories_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSeeLivewire(CategoriesIndex::class);
    }

    public function test_regular_user_cannot_access_the_categories_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.categories.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_an_expense_category(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CategoriesIndex::class)
            ->set('type', 'expense')
            ->set('name', 'مواصلات')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'مواصلات',
            'type' => 'expense',
            'is_system' => false,
        ]);
    }

    public function test_admin_can_create_an_income_category(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CategoriesIndex::class)
            ->set('type', 'income')
            ->set('name', 'راتب')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'name' => 'راتب',
            'type' => 'income',
            'is_system' => false,
        ]);
    }

    public function test_validation_rejects_invalid_data(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CategoriesIndex::class)
            ->set('name', '')
            ->set('color', 'not-a-color')
            ->call('save')
            ->assertHasErrors(['name' => 'required', 'color' => 'regex']);

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_admin_can_edit_a_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'ترفيه', 'type' => 'expense']);

        Livewire::actingAs($admin)
            ->test(CategoriesIndex::class)
            ->set('type', 'expense')
            ->call('edit', $category->id)
            ->assertSet('name', 'ترفيه')
            ->set('name', 'ترفيه وتسلية')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'ترفيه وتسلية',
        ]);
    }

    public function test_admin_can_delete_an_allowed_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['type' => 'expense', 'is_system' => false]);

        Livewire::actingAs($admin)
            ->test(CategoriesIndex::class)
            ->set('type', 'expense')
            ->call('confirmDelete', $category->id)
            ->call('delete', $category->id);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_delete_requires_prior_confirmation(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['type' => 'expense', 'is_system' => false]);

        Livewire::actingAs($admin)
            ->test(CategoriesIndex::class)
            ->set('type', 'expense')
            ->call('delete', $category->id);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_system_category_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->system()->create(['type' => 'expense']);

        Livewire::actingAs($admin)
            ->test(CategoriesIndex::class)
            ->set('type', 'expense')
            ->call('confirmDelete', $category->id)
            ->call('delete', $category->id);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_deleting_a_category_nulls_its_transactions_instead_of_deleting_them(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $category = Category::factory()->create(['type' => 'expense', 'is_system' => false]);
        $transaction = Transaction::factory()->for($user)->for($account)->create(['category_id' => $category->id]);

        Livewire::actingAs($admin)
            ->test(CategoriesIndex::class)
            ->set('type', 'expense')
            ->call('confirmDelete', $category->id)
            ->call('delete', $category->id);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('transactions', ['id' => $transaction->id, 'category_id' => null]);
    }

    public function test_deleting_a_category_cascades_its_budget_category_links(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $budget = Budget::factory()->for($user)->create();
        $category = Category::factory()->create(['type' => 'expense', 'is_system' => false]);
        $budgetCategory = BudgetCategory::factory()->for($budget)->for($category)->create();

        Livewire::actingAs($admin)
            ->test(CategoriesIndex::class)
            ->set('type', 'expense')
            ->call('confirmDelete', $category->id)
            ->call('delete', $category->id);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('budget_categories', ['id' => $budgetCategory->id]);
    }
}
