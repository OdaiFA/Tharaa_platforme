<?php

namespace Tests\Feature\Livewire\Transactions;

use App\Livewire\Transactions\TransactionsIndex;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TransactionsIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_render_transactions_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertSeeLivewire(TransactionsIndex::class);
    }

    public function test_guest_cannot_access_transactions_index(): void
    {
        $this->get(route('transactions.index'))->assertRedirect(route('login'));
    }

    public function test_user_only_sees_own_transactions(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        $otherAccount = Account::factory()->for($other)->create();

        Transaction::factory()->for($user)->for($account)->create(['description' => 'معاملتي']);
        Transaction::factory()->for($other)->for($otherAccount)->create(['description' => 'معاملة أخرى']);

        Livewire::actingAs($user)
            ->test(TransactionsIndex::class)
            ->assertSee('معاملتي')
            ->assertDontSee('معاملة أخرى');
    }

    public function test_type_filter_resets_pagination_and_scopes_results(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create();
        Transaction::factory()->for($user)->for($account)->expense()->create(['description' => 'مصروف واحد']);
        Transaction::factory()->for($user)->for($account)->income()->create(['description' => 'دخل واحد']);

        Livewire::actingAs($user)
            ->test(TransactionsIndex::class)
            ->set('type', 'expense')
            ->assertSee('مصروف واحد')
            ->assertDontSee('دخل واحد');
    }

    public function test_delete_requires_confirmation_and_recalculates_balance(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['initial_balance' => 100]);
        $transaction = Transaction::factory()->for($user)->for($account)->expense()->create(['amount' => 40]);

        Livewire::actingAs($user)
            ->test(TransactionsIndex::class)
            ->call('delete', $transaction->id);

        $this->assertModelExists($transaction);

        Livewire::actingAs($user)
            ->test(TransactionsIndex::class)
            ->call('confirmDelete', $transaction->id)
            ->call('delete', $transaction->id);

        $this->assertSoftDeleted($transaction);
    }

    public function test_user_cannot_delete_another_users_transaction(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $otherAccount = Account::factory()->for($other)->create();
        $transaction = Transaction::factory()->for($other)->for($otherAccount)->create();

        Livewire::actingAs($user)
            ->test(TransactionsIndex::class)
            ->call('confirmDelete', $transaction->id)
            ->call('delete', $transaction->id)
            ->assertNotFound();

        $this->assertModelExists($transaction);
    }
}
