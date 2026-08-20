<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use App\Repositories\AccountRepository;
use Livewire\Component;
use Livewire\WithPagination;

class AccountsIndex extends Component
{
    use WithPagination;

    public ?int $confirmingDeleteId = null;

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id, AccountRepository $accounts): void
    {
        if ($this->confirmingDeleteId !== $id) {
            return;
        }

        $account = $accounts->findForUser($id, auth()->id());

        abort_if(! $account, 404);
        $this->authorize('delete', $account);

        $accounts->delete($account);
        $this->confirmingDeleteId = null;
        session()->flash('success', 'تم حذف الحساب بنجاح');
    }

    public function render(AccountRepository $accounts)
    {
        $list = $accounts->query(auth()->id())->withCount('transactions')->paginate(10);

        return view('livewire.accounts.accounts-index', [
            'accounts' => $list,
        ]);
    }
}
