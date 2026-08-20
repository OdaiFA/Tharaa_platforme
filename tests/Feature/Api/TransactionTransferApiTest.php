<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTransferApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_same_currency_transfer_within_balance_succeeds(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 1000, 'balance' => 1000]);
        $to = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        $response = $this->postJson('/api/transactions', [
            'account_id' => $from->id,
            'type' => 'transfer',
            'amount' => 200,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        $response->assertCreated();
        $this->assertEquals(800, $from->fresh()->balance);
        $this->assertEquals(200, $to->fresh()->balance);
    }

    public function test_transfer_exceeding_balance_is_rejected_with_422(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 1000, 'balance' => 1000]);
        $to = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        $response = $this->postJson('/api/transactions', [
            'account_id' => $from->id,
            'type' => 'transfer',
            'amount' => 1200,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('transactions', 0);
        $this->assertEquals(1000, $from->fresh()->balance);
    }

    public function test_cross_currency_transfer_is_rejected(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 1000, 'balance' => 1000]);
        $to = Account::factory()->for($user)->create(['currency' => 'USD', 'initial_balance' => 0, 'balance' => 0]);

        $response = $this->postJson('/api/transactions', [
            'account_id' => $from->id,
            'type' => 'transfer',
            'amount' => 100,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseCount('transactions', 0);
    }
}
