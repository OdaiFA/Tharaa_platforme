<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * IDOR regression suite (API side) for the transaction account-ownership
 * fix — a Sanctum-authenticated client must never be able to POST/PUT
 * another user's account_id/transfer_to_account_id.
 */
class TransactionOwnershipApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_transaction_with_foreign_account_is_rejected(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $other = User::factory()->create();
        $foreignAccount = Account::factory()->for($other)->create();
        $category = Category::factory()->create();

        $response = $this->postJson('/api/transactions', [
            'account_id' => $foreignAccount->id,
            'category_id' => $category->id,
            'type' => 'expense',
            'amount' => 40,
            'transaction_date' => now()->toDateString(),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('account_id');
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_put_transaction_with_foreign_account_is_rejected(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $account = Account::factory()->for($user)->create();
        $transaction = Transaction::factory()->for($user)->for($account)->create(['type' => 'expense']);
        $other = User::factory()->create();
        $foreignAccount = Account::factory()->for($other)->create();

        $response = $this->putJson("/api/transactions/{$transaction->id}", [
            'account_id' => $foreignAccount->id,
            'type' => 'expense',
            'amount' => $transaction->amount,
            'transaction_date' => now()->toDateString(),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('account_id');
        $this->assertSame($account->id, $transaction->fresh()->account_id);
    }

    public function test_post_transfer_with_foreign_source_account_is_rejected(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $other = User::factory()->create();
        $foreignAccount = Account::factory()->for($other)->create(['currency' => 'SAR', 'initial_balance' => 500, 'balance' => 500]);
        $ownAccount = Account::factory()->for($user)->create(['currency' => 'SAR']);

        $response = $this->postJson('/api/transactions', [
            'account_id' => $foreignAccount->id,
            'transfer_to_account_id' => $ownAccount->id,
            'type' => 'transfer',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('account_id');
        $this->assertDatabaseCount('transactions', 0);
        $this->assertEquals(500, $foreignAccount->fresh()->balance);
    }

    public function test_post_transfer_with_foreign_destination_account_is_rejected(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $other = User::factory()->create();
        $ownAccount = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 500, 'balance' => 500]);
        $foreignAccount = Account::factory()->for($other)->create(['currency' => 'SAR']);

        $response = $this->postJson('/api/transactions', [
            'account_id' => $ownAccount->id,
            'transfer_to_account_id' => $foreignAccount->id,
            'type' => 'transfer',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors('transfer_to_account_id');
        $this->assertDatabaseCount('transactions', 0);
        $this->assertEquals(500, $ownAccount->fresh()->balance);
    }
}
