<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_expense_reduces_account_balance(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 1000, 'balance' => 1000]);

        $service = app(TransactionService::class);
        $service->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => null,
            'type' => 'expense',
            'amount' => 250,
            'description' => 'فاتورة',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertSame(750.0, (float) $account->fresh()->balance);
    }

    public function test_income_increases_account_balance(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 500, 'balance' => 500]);

        $service = app(TransactionService::class);
        $service->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => null,
            'type' => 'income',
            'amount' => 1000,
            'description' => 'راتب',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertSame(1500.0, (float) $account->fresh()->balance);
    }

    public function test_balance_never_drops_below_zero(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 100, 'balance' => 100]);

        $service = app(TransactionService::class);
        $service->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => null,
            'type' => 'expense',
            'amount' => 500,
            'description' => 'عملية كبيرة',
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertSame(0.0, (float) $account->fresh()->balance);
    }

    public function test_transfer_moves_money_between_accounts(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $from = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 500, 'balance' => 500]);
        $to = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 100, 'balance' => 100]);

        $service = app(TransactionService::class);
        $service->transfer($from->id, $to->id, 200, 'تحويل');

        $this->assertSame(300.0, (float) $from->fresh()->balance);
        $this->assertSame(300.0, (float) $to->fresh()->balance);
    }

    public function test_transfer_to_same_account_is_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $account = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 500, 'balance' => 500]);

        $this->expectException(\InvalidArgumentException::class);

        app(TransactionService::class)->transfer($account->id, $account->id, 100);
    }

    public function test_transfer_with_insufficient_balance_is_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $from = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 50, 'balance' => 50]);
        $to = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 100, 'balance' => 100]);

        $this->expectException(\DomainException::class);

        app(TransactionService::class)->transfer($from->id, $to->id, 200);
    }

    public function test_process_recurring_is_idempotent(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 0, 'balance' => 0]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => null,
            'type' => 'expense',
            'amount' => 50,
            'is_recurring' => true,
            'transaction_date' => now()->subMonth()->toDateString(),
        ]);

        $service = app(TransactionService::class);

        $this->assertSame(1, $service->processRecurring());
        $this->assertSame(0, $service->processRecurring());

        $this->assertSame(
            1,
            Transaction::query()->where('transaction_date', now()->toDateString())->where('is_recurring', false)->count()
        );
    }

    public function test_delete_recalculates_balance(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'initial_balance' => 1000, 'balance' => 1000]);

        $service = app(TransactionService::class);
        $transaction = $service->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => null,
            'type' => 'expense',
            'amount' => 300,
            'transaction_date' => now()->toDateString(),
        ]);

        $this->assertSame(700.0, (float) $account->fresh()->balance);

        $service->delete($transaction);

        $this->assertSame(1000.0, (float) $account->fresh()->balance);
    }
}
