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

    public function totalBalanceForUser(int $userId): float
    {
        return (float) $this->query($userId)->sum('balance');
    }
}
