<?php

namespace App\Observers;

use App\Models\GoalContribution;
use App\Services\GoalService;

class GoalContributionObserver
{
    public function __construct(private readonly GoalService $service) {}

    public function created(GoalContribution $contribution): void
    {
        $goal = $contribution->goal->fresh();

        if ($goal) {
            $goal->update(['current_amount' => $goal->contributions()->sum('amount')]);
            $this->service->checkCompletion($goal);
        }
    }
}
