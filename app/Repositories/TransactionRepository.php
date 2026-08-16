<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository extends BaseRepository
{
    protected function model(): string
    {
        return Transaction::class;
    }

    public function recentForUser(int $userId, int $limit = 8)
    {
        return $this->query($userId)
            ->with(['account', 'category', 'transferToAccount'])
            ->latest('transaction_date')
            ->limit($limit)
            ->get();
    }

    public function incomeForPeriod(int $userId, $start, $end): float
    {
        return (float) $this->query($userId)
            ->income()
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('amount');
    }

    public function expenseForPeriod(int $userId, $start, $end): float
    {
        return (float) $this->query($userId)
            ->expense()
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('amount');
    }
}
