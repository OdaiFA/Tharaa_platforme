<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Models\Budget;
use App\Services\BudgetService;

class UpdateBudgetConsumption
{
    public function __construct(private readonly BudgetService $service) {}

    public function handle(TransactionCreated $event): void
    {
        $transaction = $event->transaction;

        if ($transaction->type !== 'expense' || ! $transaction->category_id) {
            return;
        }

        Budget::query()
            ->forUser($transaction->user_id)
            ->active()
            ->where('start_date', '<=', $transaction->transaction_date)
            ->where('end_date', '>=', $transaction->transaction_date)
            ->get()
            ->each(function (Budget $budget) use ($transaction) {
                // Consumption is computed on the fly; only fire alerts.
                $this->service->checkAlert($budget, $transaction);
            });
    }
}
