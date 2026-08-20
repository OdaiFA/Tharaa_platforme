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
     * KNOWN GAP — documented, not silently fixed (see
     * docs/financial-hardening/MULTI_CURRENCY_FINANCIAL_HARDENING.md,
     * "Goal Currency Rule").
     *
     * The `goals` table has no `currency` column, and `goal_contributions`'
     * `account_id` is nullable, so there is no reliable way to derive "the"
     * currency of a goal from existing schema without a migration. This
     * test documents the current, unenforced behavior: contributions from
     * accounts of different currencies are both accepted and summed
     * directly into `current_amount` with no currency tracking at all.
     * If this test ever starts failing because contribute() now rejects
     * a currency mismatch, update this test and the "Goal Currency Rule"
     * section of the doc together — don't just delete it.
     */
    public function test_contributions_from_different_currency_accounts_are_not_currently_currency_checked(): void
    {
        Event::fake([\App\Events\GoalContributionAdded::class]);

        $user = User::factory()->create();
        $sarAccount = Account::factory()->create(['user_id' => $user->id, 'currency' => 'SAR', 'balance' => 1000]);
        $usdAccount = Account::factory()->create(['user_id' => $user->id, 'currency' => 'USD', 'balance' => 1000]);
        $goal = Goal::factory()->create(['user_id' => $user->id, 'target_amount' => 10000, 'current_amount' => 0]);

        $service = app(GoalService::class);
        $service->contribute($goal, 100, $sarAccount->id);
        $service->contribute($goal, 100, $usdAccount->id);

        // Both contributions succeed and are summed together, even though
        // they are drawn from accounts in different currencies — this is
        // the exact gap flagged as GOAL CURRENCY DECISION REQUIRED.
        $this->assertSame(200.0, (float) $goal->fresh()->current_amount);
        $this->assertSame(2, $goal->contributions()->count());
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
