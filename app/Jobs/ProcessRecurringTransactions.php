<?php

namespace App\Jobs;

use App\Services\TransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessRecurringTransactions implements ShouldQueue
{
    use Queueable;

    public function handle(TransactionService $service): void
    {
        $service->processRecurring();
    }
}
