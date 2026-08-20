<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\Statistics\StatisticsDashboard;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StatisticsDashboardCurrencyTest extends TestCase
{
    use RefreshDatabase;

    private function makeTransaction(User $user, Account $account, string $type, float $amount, ?Category $category = null, $date = null): Transaction
    {
        $category ??= Category::factory()->create(['type' => $type === 'income' ? 'income' : 'expense']);

        return Transaction::factory()->for($user)->for($account)->create([
            'type' => $type,
            'category_id' => $category->id,
            'amount' => $amount,
            'transaction_date' => $date ?? now(),
        ]);
    }

    public function test_single_currency_financial_activity_remains_correct(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['currency' => 'SAR']);

        $this->makeTransaction($user, $account, 'income', 1000);
        $this->makeTransaction($user, $account, 'expense', 200);

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $series = $component->viewData('chartData')['financialActivity']['series'];
        $this->assertCount(1, $series);
        $this->assertSame('SAR', $series[0]['currency']);
        $this->assertEquals(1000.0, $series[0]['income'][0]);
        $this->assertEquals(200.0, $series[0]['expense'][0]);
    }

    public function test_two_currencies_remain_separate_series(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $sar = Account::factory()->for($user)->create(['currency' => 'SAR']);
        $usd = Account::factory()->for($user)->create(['currency' => 'USD']);

        $this->makeTransaction($user, $sar, 'income', 1000);
        $this->makeTransaction($user, $usd, 'income', 500);

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $series = collect($component->viewData('chartData')['financialActivity']['series'])->keyBy('currency');

        $this->assertCount(2, $series);
        $this->assertEquals(1000.0, $series['SAR']['income'][0]);
        $this->assertEquals(500.0, $series['USD']['income'][0]);

        // The invalid cross-currency sum (1000 + 500 = 1500) must never
        // appear anywhere in either currency's own series.
        $this->assertNotEquals(1500.0, $series['SAR']['income'][0]);
        $this->assertNotEquals(1500.0, $series['USD']['income'][0]);
    }

    public function test_three_currencies_remain_separate_series(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $sar = Account::factory()->for($user)->create(['currency' => 'SAR']);
        $usd = Account::factory()->for($user)->create(['currency' => 'USD']);
        $eur = Account::factory()->for($user)->create(['currency' => 'EUR']);

        $this->makeTransaction($user, $sar, 'income', 1000);
        $this->makeTransaction($user, $usd, 'income', 500);
        $this->makeTransaction($user, $eur, 'income', 300);

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $series = collect($component->viewData('chartData')['financialActivity']['series'])->keyBy('currency');

        $this->assertCount(3, $series);
        $this->assertEquals(1000.0, $series['SAR']['income'][0]);
        $this->assertEquals(500.0, $series['USD']['income'][0]);
        $this->assertEquals(300.0, $series['EUR']['income'][0]);
    }

    public function test_income_and_expense_are_separated_within_the_same_currency(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['currency' => 'SAR']);

        $this->makeTransaction($user, $account, 'income', 1000);
        $this->makeTransaction($user, $account, 'expense', 200);

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $series = $component->viewData('chartData')['financialActivity']['series'][0];
        $this->assertEquals(1000.0, $series['income'][0]);
        $this->assertEquals(200.0, $series['expense'][0]);
        $this->assertNotEquals($series['income'][0], $series['expense'][0]);
    }

    public function test_monthly_grouping_remains_correct_per_currency(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $sar = Account::factory()->for($user)->create(['currency' => 'SAR']);

        $this->makeTransaction($user, $sar, 'income', 100, null, now()->subMonth()->startOfMonth());
        $this->makeTransaction($user, $sar, 'income', 400, null, now()->startOfMonth());

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $data = $component->viewData('chartData')['financialActivity'];
        $months = $data['months']->values()->all();
        $series = $data['series'][0];

        $lastMonthIndex = array_search(now()->subMonth()->format('Y-m'), $months);
        $thisMonthIndex = array_search(now()->format('Y-m'), $months);

        $this->assertEquals(100.0, $series['income'][$lastMonthIndex]);
        $this->assertEquals(400.0, $series['income'][$thisMonthIndex]);
    }

    public function test_top_categories_do_not_mix_currencies(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $sar = Account::factory()->for($user)->create(['currency' => 'SAR']);
        $usd = Account::factory()->for($user)->create(['currency' => 'USD']);
        $food = Category::factory()->create(['type' => 'expense', 'name' => 'طعام']);

        $this->makeTransaction($user, $sar, 'expense', 100, $food);
        $this->makeTransaction($user, $usd, 'expense', 50, $food);

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $topCategories = $component->viewData('topCategories');

        // Two distinct rows — one per currency — never a single combined
        // "طعام = 150" figure.
        $this->assertCount(2, $topCategories);
        $sarRow = $topCategories->firstWhere('currency', 'SAR');
        $usdRow = $topCategories->firstWhere('currency', 'USD');
        $this->assertEquals(100.0, (float) $sarRow->total);
        $this->assertEquals(50.0, (float) $usdRow->total);

        $chartSeries = collect($component->viewData('chartData')['topCategories']['series'])->keyBy('currency');
        $this->assertEquals(100.0, $chartSeries['SAR']['data'][0]);
        $this->assertEquals(50.0, $chartSeries['USD']['data'][0]);
    }

    public function test_empty_financial_data_renders_safely_with_no_series(): void
    {
        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $chartData = $component->viewData('chartData');
        $this->assertCount(0, $chartData['financialActivity']['series']);
        $this->assertCount(0, $chartData['topCategories']['series']);
        $component->assertOk();
    }

    public function test_no_pii_appears_in_rendered_chart_data(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create(['name' => 'خصوصية الإحصائيات', 'email' => 'stats-privacy@example.com']);
        $account = Account::factory()->for($target)->create(['currency' => 'SAR']);
        $this->makeTransaction($target, $account, 'expense', 250, null);

        $response = $this->actingAs($admin)->get(route('admin.statistics'));

        $response->assertOk();
        $response->assertDontSee('stats-privacy@example.com');
        $response->assertDontSee('خصوصية الإحصائيات');
    }

    public function test_non_financial_statistics_remain_unchanged_by_the_currency_grouping(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->admin()->create();
        User::factory()->count(2)->create();

        $component = Livewire::actingAs($admin)->test(StatisticsDashboard::class);

        $usersByRole = $component->viewData('usersByRole');
        $this->assertSame(2, $usersByRole['admin']);
        $this->assertSame(2, $usersByRole['user']);
    }

    public function test_admin_authorization_is_unchanged(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.statistics'))
            ->assertForbidden();
    }
}
