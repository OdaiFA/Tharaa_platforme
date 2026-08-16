<?php

namespace App\Listeners;

use App\Models\Account;
use App\Services\TransactionService;
use App\Events\TransactionCreated;

class UpdateAccountBalance
{
    public function __construct(private readonly TransactionService $service) {}

    public function handle(TransactionCreated $event): void
    {
        if ($event->transaction->type === 'transfer') {
            $this->service->recalculateAccount($event->transaction->account);
            if ($event->transaction->transferToAccount) {
                $this->service->recalculateAccount($event->transaction->transferToAccount);
            }
        } else {
            $this->service->recalculateAccount($event->transaction->account);
        }
    }
}
