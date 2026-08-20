<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\Users\UserShow;
use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserShowCurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_single_currency_user_shows_one_combined_total(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();
        Account::factory()->for($target)->create(['currency' => 'SAR', 'balance' => 500]);
        Account::factory()->for($target)->create(['currency' => 'SAR', 'balance' => 250]);

        Livewire::actingAs($admin)
            ->test(UserShow::class, ['user' => $target])
            ->assertSee('750.00')
            ->assertSee('SAR');
    }

    public function test_multi_currency_user_shows_grouped_totals_not_a_combined_sum(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();
        Account::factory()->for($target)->create(['currency' => 'SAR', 'balance' => 1000]);
        Account::factory()->for($target)->create(['currency' => 'USD', 'balance' => 500]);
        Account::factory()->for($target)->create(['currency' => 'EUR', 'balance' => 300]);

        $rendered = Livewire::actingAs($admin)
            ->test(UserShow::class, ['user' => $target]);

        $rendered->assertSee('1,000.00');
        $rendered->assertSee('500.00');
        $rendered->assertSee('300.00');
        $rendered->assertSee('SAR');
        $rendered->assertSee('USD');
        $rendered->assertSee('EUR');

        // The invalid cross-currency sum (1000 + 500 + 300 = 1800) must
        // never appear as a single combined balance figure.
        $rendered->assertDontSee('1,800.00');
    }

    public function test_user_with_no_accounts_shows_zero_without_error(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(UserShow::class, ['user' => $target])
            ->assertOk()
            ->assertSee('0.00');
    }
}
