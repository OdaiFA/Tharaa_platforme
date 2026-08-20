<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardCurrencySafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_currency_dashboard_renders_grouped_totals_safely(): void
    {
        $user = User::factory()->create();
        $sar = Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 1000]);
        $usd = Account::factory()->for($user)->create(['currency' => 'USD', 'balance' => 500]);
        $eur = Account::factory()->for($user)->create(['currency' => 'EUR', 'balance' => 300]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();

        // Each currency's own total must be shown...
        $response->assertSee('1,000.00');
        $response->assertSee('500.00');
        $response->assertSee('300.00');
        $response->assertSee('SAR');
        $response->assertSee('USD');
        $response->assertSee('EUR');

        // ...and the invalid cross-currency sum (1000 + 500 + 300 = 1800)
        // must never appear as a single combined balance figure.
        $response->assertDontSee('1,800.00');
    }

    public function test_single_currency_user_dashboard_behavior_remains_correct(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 1000]);
        Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 500]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('1,500.00');
        $response->assertSee('SAR');
    }

    public function test_dashboard_income_and_expense_totals_are_grouped_by_currency(): void
    {
        $user = User::factory()->create();
        $sar = Account::factory()->for($user)->create(['currency' => 'SAR']);
        $usd = Account::factory()->for($user)->create(['currency' => 'USD']);
        $category = Category::factory()->create(['type' => 'income']);

        Transaction::factory()->for($user)->for($sar)->create([
            'type' => 'income',
            'category_id' => $category->id,
            'amount' => 400,
            'transaction_date' => now(),
        ]);
        Transaction::factory()->for($user)->for($usd)->create([
            'type' => 'income',
            'category_id' => $category->id,
            'amount' => 200,
            'transaction_date' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('400.00');
        $response->assertSee('200.00');
        // The naive cross-currency sum (400 + 200 = 600) must not appear.
        $response->assertDontSee('600.00');
    }

    public function test_user_with_no_accounts_sees_dashboard_without_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
    }
}
