<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\Account;
use App\Repositories\AccountRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(private readonly AccountRepository $accounts) {}

    public function index(): View
    {
        return view('accounts.index');
    }

    public function create(): View
    {
        return view('accounts.create');
    }

    public function store(StoreAccountRequest $request): RedirectResponse
    {
        $this->accounts->create(array_merge($request->validated(), [
            'user_id' => auth()->id(),
            'balance' => $request->input('initial_balance', 0),
        ]));

        return redirect()->route('accounts.index')->with('success', 'تم إنشاء الحساب بنجاح');
    }

    public function edit(Account $account): View
    {
        $this->authorize('update', $account);

        return view('accounts.edit', compact('account'));
    }

    public function update(UpdateAccountRequest $request, Account $account): RedirectResponse
    {
        $this->authorize('update', $account);

        $this->accounts->update($account, $request->validated());

        return redirect()->route('accounts.index')->with('success', 'تم تحديث الحساب بنجاح');
    }

    public function destroy(Account $account): RedirectResponse
    {
        $this->authorize('delete', $account);

        $this->accounts->delete($account);

        return redirect()->route('accounts.index')->with('success', 'تم حذف الحساب بنجاح');
    }
}
