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

    /**
     * Income per currency for a user within a period.
     *
     * Transactions have no currency column of their own — it is implied by
     * their account. Summing across accounts of different currencies would
     * produce the same invalid mixed total as the account-balance bug, so
     * this groups by the owning account's currency instead.
     *
     * @return array<string, float>
     */
    public function incomeForPeriod(int $userId, $start, $end): array
    {
        return $this->sumByCurrencyForType($userId, 'income', $start, $end);
    }

    /**
     * Expense per currency for a user within a period. See incomeForPeriod().
     *
     * @return array<string, float>
     */
    public function expenseForPeriod(int $userId, $start, $end): array
    {
        return $this->sumByCurrencyForType($userId, 'expense', $start, $end);
    }

    /**
     * @return array<string, float>
     */
    private function sumByCurrencyForType(int $userId, string $type, $start, $end): array
    {
        // Both `transactions` and `accounts` have `user_id`/`type` columns —
        // every condition below is explicitly table-qualified to avoid an
        // "ambiguous column" error once the two are joined.
        return Transaction::query()
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->where('transactions.user_id', $userId)
            ->where('transactions.type', $type)
            ->whereBetween('transactions.transaction_date', [$start, $end])
            ->whereNull('transactions.deleted_at')
            ->selectRaw('accounts.currency as currency, SUM(transactions.amount) as total')
            ->groupBy('accounts.currency')
            ->orderBy('accounts.currency')
            ->pluck('total', 'currency')
            ->map(fn ($total) => (float) $total)
            ->all();
    }
}
