<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;

class UserShow extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function render()
    {
        $totals = [
            'balance' => (float) $this->user->accounts()->sum('balance'),
            'transactions' => $this->user->transactions()->count(),
            'enrollments' => $this->user->enrollments()->count(),
        ];

        return view('livewire.admin.users.user-show', [
            'totals' => $totals,
        ]);
    }
}
