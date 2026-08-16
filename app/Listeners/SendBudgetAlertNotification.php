<?php

namespace App\Listeners;

use App\Events\BudgetThresholdReached;
use App\Services\NotificationService;

class SendBudgetAlertNotification
{
    public function __construct(private readonly NotificationService $service) {}

    public function handle(BudgetThresholdReached $event): void
    {
        $this->service->sendBudgetAlert($event->budget->user, $event->budget);
    }
}
