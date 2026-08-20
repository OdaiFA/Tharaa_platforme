<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardCurrencySafetyTest extends TestCase
{
    use RefreshDatabase;

    private function createTransaction(User $user, Account $account, float $amount): void
    {
        $category = Category::factory()->create(['type' => 'expense']);

        Transaction::factory()->for($user)->for($account)->create([
            'type' => 'expense',
            'category_id' => $category->id,
            'amount' => $amount,
            'transaction_date' => now(),
        ]);
    }

    public function test_single_currency_transaction_volume_renders_one_total(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $sar = Account::factory()->for($user)->create(['currency' => 'SAR']);

        $this->createTransaction($user, $sar, 1000);
        $this->createTransaction($user, $sar, 200);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('1,200.00');
        $response->assertSee('SAR');
    }

    public function test_multi_currency_transaction_volume_renders_grouped_totals(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $sar = Account::factory()->for($user)->create(['currency' => 'SAR']);
        $usd = Account::factory()->for($user)->create(['currency' => 'USD']);
        $eur = Account::factory()->for($user)->create(['currency' => 'EUR']);

        $this->createTransaction($user, $sar, 1000);
        $this->createTransaction($user, $usd, 500);
        $this->createTransaction($user, $eur, 300);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('1,000.00');
        $response->assertSee('500.00');
        $response->assertSee('300.00');
        $response->assertSee('SAR');
        $response->assertSee('USD');
        $response->assertSee('EUR');

        // The invalid cross-currency sum (1000 + 500 + 300 = 1800) must
        // never appear as a single combined transaction-volume figure.
        $response->assertDontSee('1,800.00');
    }

    public function test_dashboard_renders_without_error_when_there_are_no_transactions(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk();
    }

    public function test_regular_user_cannot_access_the_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.dashboard'))->assertForbidden();
    }
}
