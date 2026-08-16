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
