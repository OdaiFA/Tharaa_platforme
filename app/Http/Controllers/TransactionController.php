<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Account;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionRepository $transactions,
        private readonly TransactionService $transactionService,
    ) {}

    public function index(): View
    {
        return view('transactions.index');
    }

    public function create(): View
    {
        return view('transactions.create');
    }

    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $this->transactionService->create(array_merge($request->validated(), [
            'user_id' => auth()->id(),
        ]));

        return redirect()->route('transactions.index')->with('success', 'تم إضافة المعاملة بنجاح');
    }

    public function edit(Transaction $transaction): View
    {
        $this->authorize('update', $transaction);

        return view('transactions.edit', compact('transaction'));
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $this->authorize('update', $transaction);

        $this->transactionService->update($transaction, $request->validated());

        return redirect()->route('transactions.index')->with('success', 'تم تحديث المعاملة بنجاح');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $this->authorize('delete', $transaction);

        $this->transactionService->delete($transaction);

        return redirect()->route('transactions.index')->with('success', 'تم حذف المعاملة وإعادة احتساب الرصيد');
    }
}
