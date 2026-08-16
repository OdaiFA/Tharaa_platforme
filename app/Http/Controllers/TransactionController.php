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
        $transactions = $this->transactions->query(auth()->id())
            ->with(['account', 'category', 'transferToAccount'])
            ->when(request('type'), fn ($q, $type) => $q->where('type', $type))
            ->when(request('account_id'), fn ($q, $id) => $q->where('account_id', $id))
            ->when(request('from'), fn ($q, $from) => $q->whereDate('transaction_date', '>=', $from))
            ->when(request('to'), fn ($q, $to) => $q->whereDate('transaction_date', '<=', $to))
            ->latest('transaction_date')
            ->paginate(15);

        $accounts = auth()->user()->accounts()->active()->get();

        return view('transactions.index', compact('transactions', 'accounts'));
    }

    public function create(): View
    {
        $accounts = auth()->user()->accounts()->active()->get();
        $incomeCategories = \App\Models\Category::query()->income()->orderBy('name')->get();
        $expenseCategories = \App\Models\Category::query()->expense()->orderBy('name')->get();

        return view('transactions.create', compact('accounts', 'incomeCategories', 'expenseCategories'));
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

        $accounts = auth()->user()->accounts()->active()->get();
        $incomeCategories = \App\Models\Category::query()->income()->orderBy('name')->get();
        $expenseCategories = \App\Models\Category::query()->expense()->orderBy('name')->get();

        return view('transactions.edit', compact('transaction', 'accounts', 'incomeCategories', 'expenseCategories'));
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
