<?php

namespace App\Listeners;

use App\Events\UserRegistered;
use App\Services\AuthService;

class AssignAgeGroup
{
    public function __construct(private readonly AuthService $service) {}

    public function handle(UserRegistered $event): void
    {
        $this->service->assignAgeGroup($event->user);
    }
}
