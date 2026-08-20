<?php

namespace App\Livewire\Accounts;

use App\Models\Account;
use App\Repositories\AccountRepository;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AccountForm extends Component
{
    public ?int $accountId = null;

    public string $name = '';

    public string $type = 'bank';

    public string $currency = 'SAR';

    public string $initial_balance = '0';

    public ?string $description = null;

    public bool $is_active = true;

    public function mount(?int $accountId = null): void
    {
        if ($accountId) {
            $account = Account::findOrFail($accountId);
            $this->authorize('update', $account);

            $this->accountId = $account->id;
            $this->name = $account->name;
            $this->type = $account->type;
            $this->currency = $account->currency;
            $this->description = $account->description;
            $this->is_active = $account->is_active;
        } else {
            $this->currency = auth()->user()->currency;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(['cash', 'bank', 'savings', 'electronic'])],
            'currency' => ['required', 'string', 'size:3'],
            'description' => ['nullable', 'string', 'max:255'],
        ];

        if (! $this->accountId) {
            $rules['initial_balance'] = ['required', 'numeric', 'min:0'];
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'اسم الحساب مطلوب',
            'type.required' => 'نوع الحساب مطلوب',
            'type.in' => 'نوع الحساب غير صالح',
            'currency.required' => 'العملة مطلوبة',
            'currency.size' => 'رمز العملة يجب أن يكون 3 أحرف',
            'initial_balance.required' => 'الرصيد الابتدائي مطلوب',
            'initial_balance.min' => 'الرصيد الابتدائي يجب أن يكون أكبر أو يساوي صفر',
        ];
    }

    public function save(AccountRepository $accounts)
    {
        $validated = $this->validate();
        $validated['is_active'] = $this->is_active;

        if ($this->accountId) {
            $account = $accounts->find($this->accountId);
            $this->authorize('update', $account);
            $accounts->update($account, $validated);
            session()->flash('success', 'تم تحديث الحساب بنجاح');
        } else {
            $accounts->create(array_merge($validated, [
                'user_id' => auth()->id(),
                'balance' => $validated['initial_balance'],
            ]));
            session()->flash('success', 'تم إنشاء الحساب بنجاح');
        }

        return redirect()->route('accounts.index');
    }

    public function render()
    {
        return view('livewire.accounts.account-form');
    }
}
