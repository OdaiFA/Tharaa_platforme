<?php

namespace App\Livewire\Transactions;

use App\Models\Account;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Exists;
use Livewire\Component;

class TransactionForm extends Component
{
    public ?int $transactionId = null;

    public string $type = 'expense';

    public string $account_id = '';

    public string $amount = '';

    public string $category_id = '';

    public ?string $description = null;

    public string $transaction_date;

    public bool $is_recurring = false;

    public string $recurrence_type = 'monthly';

    public ?string $recurrence_end_date = null;

    public string $transfer_to_account_id = '';

    public function mount(?int $transactionId = null): void
    {
        $this->transaction_date = now()->format('Y-m-d');

        if ($transactionId) {
            $transaction = Transaction::findOrFail($transactionId);
            $this->authorize('update', $transaction);

            $this->transactionId = $transaction->id;
            $this->type = $transaction->type;
            $this->account_id = (string) $transaction->account_id;
            $this->amount = (string) $transaction->amount;
            $this->category_id = (string) ($transaction->category_id ?? '');
            $this->description = $transaction->description;
            $this->transaction_date = optional($transaction->transaction_date)->format('Y-m-d') ?? now()->format('Y-m-d');
            $this->recurrence_end_date = optional($transaction->recurrence_end_date)->format('Y-m-d');
            $this->transfer_to_account_id = (string) ($transaction->transfer_to_account_id ?? '');
        }
    }

    protected function rules(): array
    {
        $rules = [
            'account_id' => ['required', $this->ownAccountRule()],
            'category_id' => ['nullable', 'exists:categories,id'],
            'type' => ['required', Rule::in(['income', 'expense', 'transfer'])],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
            'recurrence_end_date' => ['nullable', 'date', 'after_or_equal:transaction_date'],
            'transfer_to_account_id' => [
                'nullable',
                'required_if:type,transfer',
                $this->ownAccountRule(),
                'different:account_id',
                $this->sameCurrencyRule(),
            ],
        ];

        if (! $this->transactionId) {
            $rules['category_id'] = ['nullable', 'required_if:type,income,expense', 'exists:categories,id'];
            $rules['is_recurring'] = ['sometimes', 'boolean'];
            $rules['recurrence_type'] = ['nullable', 'required_if:is_recurring,true', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])];
        }

        return $rules;
    }

    /**
     * A user may only reference their own accounts — replaces the bare
     * `exists:accounts,id` rule (which only checked the account exists at
     * all, not that it belongs to the requester) to close an IDOR where an
     * account_id/transfer_to_account_id could be ID-guessed (e.g. via
     * browser devtools on this Livewire component's payload) to target
     * another user's account and mutate its balance.
     */
    protected function ownAccountRule(): Exists
    {
        return Rule::exists('accounts', 'id')->where('user_id', auth()->id());
    }

    /**
     * No FX conversion exists in this codebase, so a transfer must stay
     * within the same currency — otherwise `amount` would be moved unchanged
     * into a differently-valued currency, which is financially incorrect.
     */
    protected function sameCurrencyRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail): void {
            if (! $value || ! $this->account_id) {
                return;
            }

            $from = Account::find($this->account_id);
            $to = Account::find($value);

            if ($from && $to && $from->currency !== $to->currency) {
                $fail('لا يمكن التحويل بين حسابين بعملتين مختلفتين');
            }
        };
    }

    protected function messages(): array
    {
        return [
            'account_id.required' => 'الحساب مطلوب',
            'account_id.exists' => 'الحساب غير موجود',
            'category_id.required_if' => 'التصنيف مطلوب لهذا النوع من المعاملات',
            'category_id.exists' => 'التصنيف غير موجود',
            'type.required' => 'نوع المعاملة مطلوب',
            'type.in' => 'نوع المعاملة غير صالح',
            'amount.required' => 'المبلغ مطلوب',
            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'transaction_date.required' => 'تاريخ المعاملة مطلوب',
            'recurrence_type.required_if' => 'نوع التكرار مطلوب للمعاملات المتكررة',
            'transfer_to_account_id.required_if' => 'الحساب المحوَّل إليه مطلوب',
            'transfer_to_account_id.exists' => 'الحساب المحوَّل إليه غير موجود',
            'transfer_to_account_id.different' => 'لا يمكن التحويل إلى نفس الحساب',
        ];
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function save(\App\Services\TransactionService $transactionService)
    {
        $validated = $this->validate();
        $validated['category_id'] = $validated['category_id'] ?: null;
        $validated['transfer_to_account_id'] = $validated['transfer_to_account_id'] ?: null;

        try {
            if ($this->transactionId) {
                $transaction = Transaction::findOrFail($this->transactionId);
                $this->authorize('update', $transaction);
                $transactionService->update($transaction, $validated);
                session()->flash('success', 'تم تحديث المعاملة بنجاح');
            } else {
                $validated['is_recurring'] = $this->is_recurring;
                $transactionService->create(array_merge($validated, [
                    'user_id' => auth()->id(),
                ]));
                session()->flash('success', 'تم إضافة المعاملة بنجاح');
            }
        } catch (\DomainException|\InvalidArgumentException $e) {
            // TransactionService enforces same-currency + sufficient-balance
            // for transfers regardless of caller — surface it as an inline
            // form error instead of an uncaught exception.
            $this->addError('amount', $e->getMessage());

            return;
        }

        return redirect()->route('transactions.index');
    }

    public function render()
    {
        return view('livewire.transactions.transaction-form', [
            'accounts' => auth()->user()->accounts()->active()->get(),
            'incomeCategories' => Category::query()->income()->orderBy('name')->get(),
            'expenseCategories' => Category::query()->expense()->orderBy('name')->get(),
        ]);
    }
}
