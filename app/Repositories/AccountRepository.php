<?php

namespace App\Repositories;

use App\Models\Account;

class AccountRepository extends BaseRepository
{
    protected function model(): string
    {
        return Account::class;
    }

    public function activeForUser(int $userId)
    {
        return $this->query($userId)->active()->orderBy('name')->get();
    }

    /**
     * Total balance per currency for a user.
     *
     * Account balances are never summed across currencies — a SAR balance
     * and a USD balance are not the same unit, and this codebase has no
     * exchange-rate source to convert between them. The result is grouped
     * by currency instead of collapsed into one meaningless scalar.
     *
     * @return array<string, float> e.g. ['SAR' => 1000.0, 'USD' => 500.0]
     */
    public function totalBalanceForUser(int $userId): array
    {
        return $this->query($userId)
            ->selectRaw('currency, SUM(balance) as total')
            ->groupBy('currency')
            ->orderBy('currency')
            ->pluck('total', 'currency')
            ->map(fn ($total) => (float) $total)
            ->all();
    }
}
