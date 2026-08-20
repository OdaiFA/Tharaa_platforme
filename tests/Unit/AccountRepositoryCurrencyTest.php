<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\User;
use App\Repositories\AccountRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountRepositoryCurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_currency_accounts_aggregate_correctly(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 1000]);
        Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 500]);

        $totals = app(AccountRepository::class)->totalBalanceForUser($user->id);

        $this->assertSame(['SAR' => 1500.0], $totals);
    }

    public function test_different_currencies_are_not_combined_into_one_scalar_total(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 1000]);
        Account::factory()->for($user)->create(['currency' => 'USD', 'balance' => 500]);

        $totals = app(AccountRepository::class)->totalBalanceForUser($user->id);

        // Must never collapse into a single "1500" — that would silently
        // mix two different currencies into one meaningless number.
        $this->assertIsArray($totals);
        $this->assertCount(2, $totals);
        $this->assertSame(1000.0, $totals['SAR']);
        $this->assertSame(500.0, $totals['USD']);
    }

    public function test_grouped_totals_are_correct_per_currency(): void
    {
        $user = User::factory()->create();
        Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 1000]);
        Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 250]);
        Account::factory()->for($user)->create(['currency' => 'USD', 'balance' => 500]);
        Account::factory()->for($user)->create(['currency' => 'EUR', 'balance' => 300]);

        $totals = app(AccountRepository::class)->totalBalanceForUser($user->id);

        $this->assertSame([
            'EUR' => 300.0,
            'SAR' => 1250.0,
            'USD' => 500.0,
        ], $totals);
    }

    public function test_user_only_sees_their_own_currency_totals(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 1000]);
        Account::factory()->for($other)->create(['currency' => 'USD', 'balance' => 9999]);

        $totals = app(AccountRepository::class)->totalBalanceForUser($user->id);

        $this->assertSame(['SAR' => 1000.0], $totals);
    }
}
