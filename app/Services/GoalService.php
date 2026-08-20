<?php

namespace App\Services;

use App\Events\GoalContributionAdded;
use App\Models\Account;
use App\Models\Goal;
use App\Models\GoalContribution;
use Illuminate\Support\Facades\DB;

class GoalService
{
    public function __construct(
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Add a contribution to a goal (BR-FIN-009, BR-FIN-011).
     */
    public function contribute(Goal $goal, float $amount, ?int $accountId = null, string $note = null, $date = null): GoalContribution
    {
        if ((float) $goal->target_amount <= 0) {
            throw new \InvalidArgumentException('مبلغ الهدف يجب أن يكون أكبر من صفر');
        }

        if ($amount <= 0) {
            throw new \InvalidArgumentException('مبلغ المساهمة يجب أن يكون أكبر من صفر');
        }

        if ($goal->status === 'completed') {
            throw new \DomainException('لا يمكن إضافة مساهمات لهدف مكتمل');
        }

        if ($accountId) {
            $account = Account::forUser($goal->user_id)->findOrFail($accountId);

            // No exchange-rate source exists in this codebase — a
            // contribution drawn from an account in a different currency
            // than the goal would silently misrepresent its value (the same
            // MVP-safe same-currency rule already applied to transfers).
            if ($account->currency !== $goal->currency_code) {
                throw new \InvalidArgumentException('لا يمكن المساهمة بعملة مختلفة عن عملة الهدف');
            }

            if ((float) $account->balance < $amount) {
                throw new \DomainException('رصيد الحساب غير كافٍ');
            }
        }

        $contribution = DB::transaction(function () use ($goal, $amount, $accountId, $note, $date) {
            $contribution = GoalContribution::create([
                'goal_id' => $goal->id,
                'account_id' => $accountId,
                'amount' => $amount,
                'contribution_date' => $date ?? now()->toDateString(),
                'note' => $note,
            ]);

            return $contribution;
        });

        GoalContributionAdded::dispatch($goal, $contribution);

        return $contribution;
    }

    /**
     * Mark the goal as completed when current_amount >= target_amount (BR-FIN-011).
     */
    public function checkCompletion(Goal $goal): bool
    {
        if ((float) $goal->current_amount >= (float) $goal->target_amount && $goal->status !== 'completed') {
            $goal->update(['status' => 'completed']);

            $this->notificationService->send($goal->user, 'goal_completed', [
                'title' => 'هدف مكتمل 🎉',
                'message' => "مبروك! لقد حققت هدفك «{$goal->name}».",
                'action_url' => route('goals.index'),
            ], ['in_app']);

            return true;
        }

        return false;
    }

    /**
     * Build milestones for the goal progress visualization.
     *
     * @return array<int, array{percentage: int, achieved: bool}>
     */
    public function getMilestones(Goal $goal): array
    {
        $steps = [25, 50, 75, 100];
        $progress = $goal->progress_percentage;

        return array_map(fn (int $step) => [
            'percentage' => $step,
            'achieved' => $progress >= $step,
        ], $steps);
    }
}
