<?php

namespace Tests\Unit;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BudgetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BudgetServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_consumption_is_calculated_from_expenses_in_period(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['type' => 'expense']);
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 1000,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 250,
            'transaction_date' => now()->toDateString(),
        ]);

        $consumption = app(BudgetService::class)->calculateConsumption($budget);

        $this->assertSame(250.0, $consumption['spent']);
        $this->assertSame(750.0, $consumption['remaining']);
        $this->assertSame(25, $consumption['percentage']);
    }

    public function test_expenses_outside_period_are_ignored(): void
    {
        $user = User::factory()->create();
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 1000,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'amount' => 500,
            'transaction_date' => now()->subMonths(2)->toDateString(),
        ]);

        $consumption = app(BudgetService::class)->calculateConsumption($budget);

        $this->assertSame(0.0, $consumption['spent']);
    }

    public function test_alert_triggered_when_threshold_reached(): void
    {
        $user = User::factory()->create();
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 1000,
            'alert_percentage' => 80,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'amount' => 800,
            'transaction_date' => now()->toDateString(),
        ]);

        $service = app(BudgetService::class);

        $this->assertTrue($service->checkAlert($budget));

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertSame(90, $service->calculateConsumption($budget)['percentage']);
    }

    public function test_consumption_only_includes_transactions_matching_the_budgets_currency(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['type' => 'expense']);
        $sarAccount = \App\Models\Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR']);
        $usdAccount = \App\Models\Account::factory()->create(['user_id' => $user->id, 'currency' => 'USD']);

        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'currency' => 'SAR',
            'total_amount' => 1000,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'account_id' => $sarAccount->id,
            'category_id' => $category->id,
            'amount' => 250,
            'transaction_date' => now()->toDateString(),
        ]);

        // A USD expense must never inflate a SAR budget's consumption.
        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'account_id' => $usdAccount->id,
            'category_id' => $category->id,
            'amount' => 900,
            'transaction_date' => now()->toDateString(),
        ]);

        $consumption = app(BudgetService::class)->calculateConsumption($budget);

        $this->assertSame(250.0, $consumption['spent']);
    }

    public function test_category_consumption_only_includes_transactions_matching_the_budgets_currency(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['type' => 'expense']);
        $sarAccount = \App\Models\Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR']);
        $usdAccount = \App\Models\Account::factory()->create(['user_id' => $user->id, 'currency' => 'USD']);

        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'currency' => 'SAR',
            'total_amount' => 5000,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        \App\Models\BudgetCategory::factory()->create([
            'budget_id' => $budget->id,
            'category_id' => $category->id,
            'limit_amount' => 400,
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'account_id' => $sarAccount->id,
            'category_id' => $category->id,
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'account_id' => $usdAccount->id,
            'category_id' => $category->id,
            'amount' => 300,
            'transaction_date' => now()->toDateString(),
        ]);

        $rows = app(BudgetService::class)->calculateCategoryConsumption($budget);

        $this->assertCount(1, $rows);
        $this->assertSame(100.0, $rows[0]['spent']);
    }

    public function test_category_consumption_respects_category_limit(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['type' => 'expense']);
        $budget = Budget::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 5000,
            'start_date' => now()->startOfMonth()->toDateString(),
            'end_date' => now()->endOfMonth()->toDateString(),
        ]);

        $budgetCategory = \App\Models\BudgetCategory::factory()->create([
            'budget_id' => $budget->id,
            'category_id' => $category->id,
            'limit_amount' => 400,
            'alert_percentage' => 90,
        ]);

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 200,
            'transaction_date' => now()->toDateString(),
        ]);

        $rows = app(BudgetService::class)->calculateCategoryConsumption($budget);

        $this->assertCount(1, $rows);
        $this->assertSame(200.0, $rows[0]['spent']);
        $this->assertSame(50, $rows[0]['percentage']);
        $this->assertSame(200.0, $rows[0]['remaining']);

        $this->assertFalse(app(BudgetService::class)->checkCategoryAlert($budget, $budgetCategory));

        Transaction::factory()->expense()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'amount' => 200,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertTrue(app(BudgetService::class)->checkCategoryAlert($budget, $budgetCategory));
    }
}
