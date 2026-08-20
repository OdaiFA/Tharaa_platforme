<?php

namespace Tests\Feature\Api;

use App\Models\Account;
use App\Models\Goal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GoalCurrencyApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_creating_a_goal_without_currency_code_falls_back_to_the_users_currency(): void
    {
        // Non-breaking for existing API clients that predate goal currency.
        $user = User::factory()->create(['currency' => 'SAR']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/goals', [
            'name' => 'هدف قديم العميل',
            'target_amount' => 1000,
            'deadline' => now()->addMonth()->toDateString(),
            'priority' => 'medium',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.currency_code', 'SAR');
        $this->assertDatabaseHas('goals', ['user_id' => $user->id, 'currency_code' => 'SAR']);
    }

    public function test_creating_a_goal_with_an_explicit_currency_code(): void
    {
        $user = User::factory()->create(['currency' => 'SAR']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/goals', [
            'name' => 'هدف دولار',
            'target_amount' => 1000,
            'currency_code' => 'USD',
            'deadline' => now()->addMonth()->toDateString(),
            'priority' => 'medium',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('data.currency_code', 'USD');
    }

    public function test_contribution_from_a_different_currency_account_is_rejected_with_422(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $usdAccount = Account::factory()->for($user)->create(['currency' => 'USD', 'balance' => 1000]);
        $goal = Goal::factory()->for($user)->create(['currency_code' => 'SAR', 'target_amount' => 5000, 'current_amount' => 0]);

        $response = $this->postJson("/api/goals/{$goal->id}/contribute", [
            'amount' => 100,
            'account_id' => $usdAccount->id,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(0, $goal->fresh()->current_amount);
    }

    public function test_contribution_from_the_same_currency_account_succeeds(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $sarAccount = Account::factory()->for($user)->create(['currency' => 'SAR', 'balance' => 1000]);
        $goal = Goal::factory()->for($user)->create(['currency_code' => 'SAR', 'target_amount' => 5000, 'current_amount' => 0]);

        $response = $this->postJson("/api/goals/{$goal->id}/contribute", [
            'amount' => 100,
            'account_id' => $sarAccount->id,
        ]);

        $response->assertCreated();
        $this->assertEquals(100, $goal->fresh()->current_amount);
    }

    public function test_update_endpoint_cannot_change_currency_code(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $goal = Goal::factory()->for($user)->create(['currency_code' => 'SAR']);

        $response = $this->putJson("/api/goals/{$goal->id}", [
            'name' => $goal->name,
            'target_amount' => $goal->target_amount,
            'currency_code' => 'USD',
            'deadline' => now()->addMonth()->toDateString(),
            'priority' => 'medium',
        ]);

        $response->assertOk();
        $this->assertSame('SAR', $goal->fresh()->currency_code);
    }
}
