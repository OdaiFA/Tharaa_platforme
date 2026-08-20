<?php

namespace Tests\Feature\Livewire\Transactions;

use App\Livewire\Transactions\TransactionForm;
use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_an_expense_transaction_and_balance_is_recalculated(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['initial_balance' => 100]);
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(TransactionForm::class)
            ->set('type', 'expense')
            ->set('account_id', (string) $account->id)
            ->set('category_id', (string) $category->id)
            ->set('amount', '40')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'amount' => 40,
        ]);
        $this->assertEquals(60, $account->fresh()->balance);
    }

    public function test_user_can_create_a_transfer_transaction_using_transfer_to_account_id(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->for($user)->create(['initial_balance' => 200]);
        $to = Account::factory()->for($user)->create(['initial_balance' => 0]);

        Livewire::actingAs($user)
            ->test(TransactionForm::class)
            ->set('type', 'transfer')
            ->set('account_id', (string) $from->id)
            ->set('transfer_to_account_id', (string) $to->id)
            ->set('amount', '50')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('transactions', [
            'type' => 'transfer',
            'account_id' => $from->id,
            'transfer_to_account_id' => $to->id,
            'amount' => 50,
        ]);
        $this->assertEquals(150, $from->fresh()->balance);
        $this->assertEquals(50, $to->fresh()->balance);
    }

    public function test_transfer_to_same_account_is_rejected(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();

        Livewire::actingAs($user)
            ->test(TransactionForm::class)
            ->set('type', 'transfer')
            ->set('account_id', (string) $account->id)
            ->set('transfer_to_account_id', (string) $account->id)
            ->set('amount', '10')
            ->call('save')
            ->assertHasErrors(['transfer_to_account_id']);
    }

    public function test_cross_currency_transfer_is_rejected(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 500]);
        $to = Account::factory()->for($user)->create(['currency' => 'USD', 'initial_balance' => 0]);

        Livewire::actingAs($user)
            ->test(TransactionForm::class)
            ->set('type', 'transfer')
            ->set('account_id', (string) $from->id)
            ->set('transfer_to_account_id', (string) $to->id)
            ->set('amount', '50')
            ->call('save')
            ->assertHasErrors(['transfer_to_account_id']);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_transfer_within_balance_succeeds(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 1000]);
        $to = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 0]);

        Livewire::actingAs($user)
            ->test(TransactionForm::class)
            ->set('type', 'transfer')
            ->set('account_id', (string) $from->id)
            ->set('transfer_to_account_id', (string) $to->id)
            ->set('amount', '200')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals(800, $from->fresh()->balance);
        $this->assertEquals(200, $to->fresh()->balance);
    }

    public function test_transfer_exceeding_balance_is_rejected(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 1000, 'balance' => 1000]);
        $to = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        Livewire::actingAs($user)
            ->test(TransactionForm::class)
            ->set('type', 'transfer')
            ->set('account_id', (string) $from->id)
            ->set('transfer_to_account_id', (string) $to->id)
            ->set('amount', '1200')
            ->call('save')
            ->assertHasErrors(['amount']);

        $this->assertDatabaseCount('transactions', 0);
        $this->assertEquals(1000, $from->fresh()->balance);
    }

    public function test_required_fields_are_validated(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(TransactionForm::class)
            ->set('account_id', '')
            ->set('amount', '')
            ->call('save')
            ->assertHasErrors(['account_id', 'amount']);
    }

    public function test_user_can_edit_their_own_transaction(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $transaction = Transaction::factory()->for($user)->for($account)->create(['description' => 'قديم']);

        Livewire::actingAs($user)
            ->test(TransactionForm::class, ['transactionId' => $transaction->id])
            ->assertSet('description', 'قديم')
            ->set('description', 'محدث')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('محدث', $transaction->fresh()->description);
    }

    public function test_user_cannot_edit_another_users_transaction(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $otherAccount = Account::factory()->for($other)->create();
        $transaction = Transaction::factory()->for($other)->for($otherAccount)->create();

        Livewire::actingAs($user)
            ->test(TransactionForm::class, ['transactionId' => $transaction->id])
            ->assertForbidden();
    }

    public function test_guest_cannot_access_the_create_form(): void
    {
        $this->get(route('transactions.create'))->assertRedirect(route('login'));
    }

    /**
     * IDOR regression: a user must not be able to create a transaction
     * against another user's account by submitting its ID directly (e.g.
     * via browser devtools tampering with this Livewire component's
     * payload).
     */
    public function test_transaction_form_rejects_foreign_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $foreignAccount = Account::factory()->for($other)->create();
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(TransactionForm::class)
            ->set('type', 'expense')
            ->set('account_id', (string) $foreignAccount->id)
            ->set('category_id', (string) $category->id)
            ->set('amount', '40')
            ->call('save')
            ->assertHasErrors(['account_id']);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_transaction_form_rejects_foreign_transfer_account(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $ownAccount = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 500, 'balance' => 500]);
        $foreignAccount = Account::factory()->for($other)->create(['currency' => 'SAR']);

        Livewire::actingAs($user)
            ->test(TransactionForm::class)
            ->set('type', 'transfer')
            ->set('account_id', (string) $ownAccount->id)
            ->set('transfer_to_account_id', (string) $foreignAccount->id)
            ->set('amount', '50')
            ->call('save')
            ->assertHasErrors(['transfer_to_account_id']);

        $this->assertDatabaseCount('transactions', 0);
        $this->assertEquals(500, $ownAccount->fresh()->balance);
    }
}
