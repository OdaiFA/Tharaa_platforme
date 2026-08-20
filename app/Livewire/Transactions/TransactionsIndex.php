<?php

namespace App\Livewire\Transactions;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\TransactionService;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionsIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $type = '';

    #[Url]
    public string $account_id = '';

    #[Url]
    public string $from = '';

    #[Url]
    public string $to = '';

    public ?int $confirmingDeleteId = null;

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedAccountId(): void
    {
        $this->resetPage();
    }

    public function updatedFrom(): void
    {
        $this->resetPage();
    }

    public function updatedTo(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->reset(['type', 'account_id', 'from', 'to']);
        $this->resetPage();
    }

    public function confirmDelete(int $id): void
    {
        $this->confirmingDeleteId = $id;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDeleteId = null;
    }

    public function delete(int $id, TransactionRepository $transactions, TransactionService $transactionService): void
    {
        if ($this->confirmingDeleteId !== $id) {
            return;
        }

        $transaction = $transactions->findForUser($id, auth()->id());

        abort_if(! $transaction, 404);
        $this->authorize('delete', $transaction);

        $transactionService->delete($transaction);
        $this->confirmingDeleteId = null;
        session()->flash('success', 'تم حذف المعاملة وإعادة احتساب الرصيد');
    }

    public function render(TransactionRepository $transactions)
    {
        $list = $transactions->query(auth()->id())
            ->with(['account', 'category', 'transferToAccount'])
            ->when($this->type !== '', fn ($q) => $q->where('type', $this->type))
            ->when($this->account_id !== '', fn ($q) => $q->where('account_id', $this->account_id))
            ->when($this->from !== '', fn ($q) => $q->whereDate('transaction_date', '>=', $this->from))
            ->when($this->to !== '', fn ($q) => $q->whereDate('transaction_date', '<=', $this->to))
            ->latest('transaction_date')
            ->paginate(15);

        $accounts = auth()->user()->accounts()->active()->get();

        return view('livewire.transactions.transactions-index', [
            'transactions' => $list,
            'accounts' => $accounts,
        ]);
    }
}
