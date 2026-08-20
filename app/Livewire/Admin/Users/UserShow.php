<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use App\Repositories\AccountRepository;
use Livewire\Component;

class UserShow extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function render(AccountRepository $accounts)
    {
        $totals = [
            // Grouped by currency via the same repository method used by the
            // user dashboard — never summed across currencies. See
            // AccountRepository::totalBalanceForUser().
            'balanceByCurrency' => $accounts->totalBalanceForUser($this->user->id),
            'transactions' => $this->user->transactions()->count(),
            'enrollments' => $this->user->enrollments()->count(),
        ];

        return view('livewire.admin.users.user-show', [
            'totals' => $totals,
        ]);
    }
}
