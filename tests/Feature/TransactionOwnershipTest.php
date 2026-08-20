<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * IDOR regression suite for the transaction account-ownership fix.
 * account_id/transfer_to_account_id must always be scoped to the acting
 * user across the web (StoreTransactionRequest/UpdateTransactionRequest),
 * Livewire (TransactionForm — see TransactionFormTest), and API
 * (ApiTransactionController — see Api/TransactionOwnershipApiTest) paths.
 */
class TransactionOwnershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_own_transaction(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['initial_balance' => 100]);
        $category = Category::factory()->create();

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $account->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 40,
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'amount' => 40,
        ]);
    }

    public function test_user_cannot_create_transaction_using_another_users_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $foreignAccount = Account::factory()->for($other)->create();
        $category = Category::factory()->create();

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $foreignAccount->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 40,
            'transaction_date' => now()->toDateString(),
        ])->assertSessionHasErrors('account_id');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_user_can_transfer_between_own_accounts(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 500]);
        $to = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 0]);

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $from->id,
            'transfer_to_account_id' => $to->id,
            'type' => 'transfer',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ])->assertRedirect(route('transactions.index'));

        $this->assertEquals(400, $from->fresh()->balance);
        $this->assertEquals(100, $to->fresh()->balance);
    }

    public function test_user_cannot_transfer_to_another_users_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 500, 'balance' => 500]);
        $foreignAccount = Account::factory()->for($other)->create(['currency' => 'SAR']);

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $from->id,
            'transfer_to_account_id' => $foreignAccount->id,
            'type' => 'transfer',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ])->assertSessionHasErrors('transfer_to_account_id');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertEquals(500, $from->fresh()->balance);
    }

    public function test_user_cannot_transfer_using_another_users_account_as_the_source(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $foreignAccount = Account::factory()->for($other)->create(['currency' => 'SAR', 'initial_balance' => 500, 'balance' => 500]);
        $ownAccount = Account::factory()->for($user)->create(['currency' => 'SAR']);

        $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $foreignAccount->id,
            'transfer_to_account_id' => $ownAccount->id,
            'type' => 'transfer',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ])->assertSessionHasErrors('account_id');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertEquals(500, $foreignAccount->fresh()->balance);
    }

    public function test_user_cannot_edit_another_users_transaction_to_repoint_it_at_their_own_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $otherAccount = Account::factory()->for($other)->create();
        $transaction = Transaction::factory()->for($other)->for($otherAccount)->create();
        $ownAccount = Account::factory()->for($user)->create();

        $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'account_id' => $ownAccount->id,
            'type' => $transaction->type,
            'amount' => $transaction->amount,
            'transaction_date' => now()->toDateString(),
        ])->assertForbidden();
    }

    public function test_user_cannot_repoint_their_own_transaction_at_another_users_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $transaction = Transaction::factory()->for($user)->for($account)->create(['type' => 'expense']);
        $foreignAccount = Account::factory()->for($other)->create();

        $this->actingAs($user)->put(route('transactions.update', $transaction), [
            'account_id' => $foreignAccount->id,
            'type' => 'expense',
            'amount' => $transaction->amount,
            'transaction_date' => now()->toDateString(),
        ])->assertSessionHasErrors('account_id');

        $this->assertSame($account->id, $transaction->fresh()->account_id);
    }
}
