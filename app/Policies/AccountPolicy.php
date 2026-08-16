<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\User;

class AccountPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Account $account): bool
    {
        return $user->isAdmin() || $account->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Account $account): bool
    {
        return $account->user_id === $user->id;
    }

    public function delete(User $user, Account $account): bool
    {
        return $account->user_id === $user->id;
    }

    public function restore(User $user, Account $account): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Account $account): bool
    {
        return $user->isAdmin();
    }
}
