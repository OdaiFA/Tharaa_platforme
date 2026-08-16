<?php

namespace App\Listeners;

use App\Events\GoalContributionAdded;
use App\Services\GoalService;

class CheckGoalCompletion
{
    public function __construct(private readonly GoalService $service) {}

    public function handle(GoalContributionAdded $event): void
    {
        $this->service->checkCompletion($event->goal->fresh());
    }
}
