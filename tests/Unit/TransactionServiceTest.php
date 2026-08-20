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

    public function test_transfer_rejects_accounts_with_different_currencies(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $from = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 500, 'balance' => 500]);
        $to = Account::factory()->create(['user_id' => $user->id, 'currency' => 'USD', 'initial_balance' => 100, 'balance' => 100]);

        $this->expectException(\InvalidArgumentException::class);

        app(TransactionService::class)->transfer($from->id, $to->id, 200);
    }

    public function test_transfer_allows_accounts_with_the_same_explicit_currency(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $from = Account::factory()->create(['user_id' => $user->id, 'currency' => 'USD', 'initial_balance' => 500, 'balance' => 500]);
        $to = Account::factory()->create(['user_id' => $user->id, 'currency' => 'USD', 'initial_balance' => 100, 'balance' => 100]);

        $service = app(TransactionService::class);
        $service->transfer($from->id, $to->id, 200);

        $this->assertSame(300.0, (float) $from->fresh()->balance);
        $this->assertSame(300.0, (float) $to->fresh()->balance);
    }

    public function test_create_rejects_a_transfer_exceeding_balance_even_when_bypassing_the_transfer_helper(): void
    {
        // TransactionForm and the API controller both call create() directly
        // rather than transfer() — this proves the guard lives in create()
        // itself, not just in the (unused-by-the-UI) transfer() helper.
        $user = User::factory()->create();
        $from = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 1000, 'balance' => 1000]);
        $to = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        $this->expectException(\DomainException::class);

        app(TransactionService::class)->create([
            'user_id' => $user->id,
            'account_id' => $from->id,
            'category_id' => null,
            'type' => 'transfer',
            'amount' => 1200,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);
    }

    public function test_create_allows_a_transfer_within_balance_via_the_generic_flow(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 1000, 'balance' => 1000]);
        $to = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        app(TransactionService::class)->create([
            'user_id' => $user->id,
            'account_id' => $from->id,
            'category_id' => null,
            'type' => 'transfer',
            'amount' => 200,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        $this->assertSame(800.0, (float) $from->fresh()->balance);
        $this->assertSame(200.0, (float) $to->fresh()->balance);
    }

    public function test_create_rejects_a_cross_currency_transfer_even_when_bypassing_form_validation(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 1000, 'balance' => 1000]);
        $to = Account::factory()->create(['user_id' => $user->id, 'currency' => 'USD', 'initial_balance' => 0, 'balance' => 0]);

        $this->expectException(\InvalidArgumentException::class);

        app(TransactionService::class)->create([
            'user_id' => $user->id,
            'account_id' => $from->id,
            'category_id' => null,
            'type' => 'transfer',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);
    }

    public function test_update_rejects_increasing_a_transfer_beyond_available_balance(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 1000, 'balance' => 1000]);
        $to = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        $service = app(TransactionService::class);
        $transfer = $service->create([
            'user_id' => $user->id,
            'account_id' => $from->id,
            'category_id' => null,
            'type' => 'transfer',
            'amount' => 200,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        $this->expectException(\DomainException::class);

        $service->update($transfer, [
            'account_id' => $from->id,
            'type' => 'transfer',
            'amount' => 5000,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);
    }

    public function test_update_allows_editing_a_transfer_within_its_own_available_balance(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 1000, 'balance' => 1000]);
        $to = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        $service = app(TransactionService::class);
        $transfer = $service->create([
            'user_id' => $user->id,
            'account_id' => $from->id,
            'category_id' => null,
            'type' => 'transfer',
            'amount' => 200,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        // Raising the amount to 900 must not be rejected on account of the
        // transfer's OWN original 200 still being "in flight" — the balance
        // check must exclude this transaction's prior contribution.
        $service->update($transfer, [
            'account_id' => $from->id,
            'type' => 'transfer',
            'amount' => 900,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        $this->assertSame(100.0, (float) $from->fresh()->balance);
        $this->assertSame(900.0, (float) $to->fresh()->balance);
    }

    public function test_recurring_transfer_template_that_becomes_underfunded_is_skipped_not_fatal(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);
        $to = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $from->id,
            'category_id' => null,
            'type' => 'transfer',
            'amount' => 500,
            'is_recurring' => true,
            'transaction_date' => now()->subMonth()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        // The source account has no funds, so this recurring transfer can no
        // longer be honored — processRecurring() must skip it instead of
        // throwing and aborting the whole batch.
        $created = app(TransactionService::class)->processRecurring();

        $this->assertSame(0, $created);
        $this->assertSame(0.0, (float) $from->fresh()->balance);
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

    public function test_recurring_transaction_keeps_the_same_account_and_currency(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'currency' => 'USD', 'initial_balance' => 0, 'balance' => 0]);

        $template = Transaction::factory()->create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'category_id' => null,
            'type' => 'expense',
            'amount' => 75,
            'is_recurring' => true,
            'transaction_date' => now()->subMonth()->toDateString(),
        ]);

        app(TransactionService::class)->processRecurring();

        $generated = Transaction::query()
            ->where('transaction_date', now()->toDateString())
            ->where('is_recurring', false)
            ->firstOrFail();

        $this->assertSame($template->user_id, $generated->user_id);
        $this->assertSame($template->account_id, $generated->account_id);
        $this->assertSame($account->currency, $generated->account->currency);
        $this->assertSame($template->type, $generated->type);
        $this->assertSame(75.0, (float) $generated->amount);
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
