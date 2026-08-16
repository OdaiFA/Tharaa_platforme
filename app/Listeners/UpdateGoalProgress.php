<?php

namespace App\Listeners;

use App\Events\GoalContributionAdded;
use App\Models\Goal;

class UpdateGoalProgress
{
    public function handle(GoalContributionAdded $event): void
    {
        $goal = Goal::find($event->goal->id);

        if ($goal) {
            $goal->update([
                'current_amount' => $goal->contributions()->sum('amount'),
            ]);
        }
    }
}
