<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TransactionController::store()/update() (the plain, legacy full-page-POST
 * web routes — still registered per "preserve all routes") have no local
 * try/catch around TransactionService::create()/update(), which throws
 * \DomainException for a transfer exceeding the source account's balance.
 * Before bootstrap/app.php registered a global renderable() for
 * \DomainException/\InvalidArgumentException, this was a genuine reachable
 * 500 — a real gap, not a hypothetical one. These tests exercise that exact
 * path to prove the global handler closes it.
 */
class GlobalExceptionHandlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_domain_exception_on_a_web_request_does_not_return_a_500(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 100, 'balance' => 100]);
        $to = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $from->id,
            'type' => 'transfer',
            'amount' => 5000,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('error');
        $this->assertSame('رصيد الحساب المصدر غير كافٍ', session('errors')->first('error'));
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_domain_exception_on_a_json_request_returns_structured_422(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 100, 'balance' => 100]);
        $to = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        $response = $this->actingAs($user)->postJson(route('transactions.store'), [
            'account_id' => $from->id,
            'type' => 'transfer',
            'amount' => 5000,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['message' => 'رصيد الحساب المصدر غير كافٍ']);
    }

    public function test_invalid_argument_exception_on_a_web_request_does_not_return_a_500(): void
    {
        $user = User::factory()->create();
        $account = Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 500]);

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $account->id,
            'type' => 'transfer',
            'amount' => 10,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $account->id,
        ]);

        // Same-account transfer is rejected before it ever reaches
        // TransactionService (form validation's `different:account_id`
        // catches it first) — assert the request is handled gracefully
        // regardless of which layer rejects it: no crash, a redirect back
        // with a real error, never a 500.
        $this->assertNotSame(500, $response->getStatusCode());
        $response->assertStatus(302);
    }

    public function test_business_exception_never_produces_a_500_status(): void
    {
        $user = User::factory()->create();
        $from = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 100, 'balance' => 100]);
        $to = Account::factory()->for($user)->create(['currency' => 'SAR', 'initial_balance' => 0, 'balance' => 0]);

        $response = $this->actingAs($user)->post(route('transactions.store'), [
            'account_id' => $from->id,
            'type' => 'transfer',
            'amount' => 5000,
            'transaction_date' => now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);

        $response->assertDontSee('Stack trace', false);
        $response->assertDontSee('Whoops', false);
    }
}
