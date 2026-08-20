<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\Goal;
use App\Models\GoalContribution;
use App\Models\User;
use App\Services\GoalService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class GoalServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_contribution_increments_goal_progress(): void
    {
        Event::fake([\App\Events\GoalContributionAdded::class]);

        $user = User::factory()->create();
        $goal = Goal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 1000,
            'current_amount' => 0,
        ]);

        $contribution = app(GoalService::class)->contribute($goal, 300);

        $this->assertInstanceOf(GoalContribution::class, $contribution);
        $this->assertSame(300.0, (float) $contribution->amount);
        $this->assertSame(300.0, (float) $goal->fresh()->current_amount);
    }

    public function test_contribution_rejects_zero_or_negative_amount(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $user = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $user->id, 'target_amount' => 1000]);

        app(GoalService::class)->contribute($goal, 0);
    }

    public function test_contribution_rejects_amount_above_account_balance(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $account = Account::factory()->create(['user_id' => $user->id, 'balance' => 100]);
        $goal = Goal::factory()->create(['user_id' => $user->id, 'target_amount' => 1000]);

        $this->expectException(\DomainException::class);

        app(GoalService::class)->contribute($goal, 200, $account->id);
    }

    public function test_contribution_to_completed_goal_is_rejected(): void
    {
        Event::fake();

        $user = User::factory()->create();
        $goal = Goal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 1000,
            'current_amount' => 1000,
            'status' => 'completed',
        ]);

        $this->expectException(\DomainException::class);

        app(GoalService::class)->contribute($goal, 50);
    }

    public function test_check_completion_marks_goal_completed(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 1000,
            'current_amount' => 1000,
            'status' => 'active',
        ]);

        $completed = app(GoalService::class)->checkCompletion($goal);

        $this->assertTrue($completed);
        $this->assertSame('completed', $goal->fresh()->status);
    }

    public function test_goal_not_completed_when_below_target(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 1000,
            'current_amount' => 500,
            'status' => 'active',
        ]);

        $this->assertFalse(app(GoalService::class)->checkCompletion($goal));
        $this->assertSame('active', $goal->fresh()->status);
    }

    /**
     * The GOAL CURRENCY DECISION REQUIRED gap (documented in Batch 1/2 of
     * docs/financial-hardening/) is now resolved by `goals.currency_code`
     * (see docs/financial-hardening/GOAL_CURRENCY_ARCHITECTURE.md) — a
     * contribution from an account in a different currency than the goal
     * must now be rejected, not silently summed in.
     */
    public function test_contribution_from_a_different_currency_account_is_rejected(): void
    {
        $user = User::factory()->create();
        $usdAccount = Account::factory()->create(['user_id' => $user->id, 'currency' => 'USD', 'balance' => 1000]);
        $goal = Goal::factory()->create(['user_id' => $user->id, 'currency_code' => 'SAR', 'target_amount' => 10000, 'current_amount' => 0]);

        $this->expectException(\InvalidArgumentException::class);

        app(GoalService::class)->contribute($goal, 100, $usdAccount->id);
    }

    public function test_contribution_from_the_same_currency_account_succeeds(): void
    {
        Event::fake([\App\Events\GoalContributionAdded::class]);

        $user = User::factory()->create();
        $sarAccount = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'balance' => 1000]);
        $goal = Goal::factory()->create(['user_id' => $user->id, 'currency_code' => 'SAR', 'target_amount' => 10000, 'current_amount' => 0]);

        app(GoalService::class)->contribute($goal, 100, $sarAccount->id);

        $this->assertSame(100.0, (float) $goal->fresh()->current_amount);
    }

    public function test_off_book_contribution_without_an_account_is_unaffected_by_currency_checking(): void
    {
        Event::fake([\App\Events\GoalContributionAdded::class]);

        $user = User::factory()->create();
        $goal = Goal::factory()->create(['user_id' => $user->id, 'currency_code' => 'SAR', 'target_amount' => 10000, 'current_amount' => 0]);

        // No account_id means there is nothing to compare the goal's
        // currency against — a manual/off-book contribution is unaffected.
        app(GoalService::class)->contribute($goal, 100, null);

        $this->assertSame(100.0, (float) $goal->fresh()->current_amount);
    }

    public function test_milestones_reflect_progress(): void
    {
        $user = User::factory()->create();
        $goal = Goal::factory()->create([
            'user_id' => $user->id,
            'target_amount' => 1000,
            'current_amount' => 250,
        ]);

        $milestones = app(GoalService::class)->getMilestones($goal);

        $this->assertCount(4, $milestones);
        $this->assertTrue($milestones[0]['achieved']);
        $this->assertFalse($milestones[1]['achieved']);
        $this->assertFalse($milestones[2]['achieved']);
        $this->assertFalse($milestones[3]['achieved']);
    }
}
