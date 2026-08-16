<?php

namespace App\Services;

use App\Events\BudgetThresholdReached;
use App\Events\TransactionCreated;
use App\Models\Account;
use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        private readonly BudgetService $budgetService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Create a transaction and apply balance/budget side effects.
     */
    public function create(array $data): Transaction
    {
        return DB::transaction(function () use ($data) {
            $transaction = Transaction::create($data);

            $this->recalculateAccounts($transaction);

            if ($transaction->type === 'expense') {
                $this->updateBudgetConsumption($transaction);
            }

            TransactionCreated::dispatch($transaction);

            return $transaction;
        });
    }

    /**
     * Update a transaction and recalculate affected balances.
     */
    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $oldAccountIds = $transaction->type === 'transfer'
                ? [$transaction->account_id, $transaction->transfer_to_account_id]
                : [$transaction->account_id];

            $transaction->update($data);

            $newAccountIds = $transaction->type === 'transfer'
                ? [$transaction->account_id, $transaction->transfer_to_account_id]
                : [$transaction->account_id];

            foreach (array_unique(array_merge($oldAccountIds, $newAccountIds)) as $accountId) {
                $account = Account::find($accountId);
                if ($account) {
                    $this->recalculateAccount($account);
                }
            }

            if ($transaction->type === 'expense') {
                $this->updateBudgetConsumption($transaction);
            }

            return $transaction->fresh();
        });
    }

    /**
     * Delete a transaction and recalculate the affected account balances.
     *
     * @throws \Exception
     */
    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $accountIds = $transaction->type === 'transfer'
                ? [$transaction->account_id, $transaction->transfer_to_account_id]
                : [$transaction->account_id];

            $transaction->delete();

            foreach (array_unique($accountIds) as $accountId) {
                $account = Account::find($accountId);
                if ($account) {
                    $this->recalculateAccount($account);
                }
            }

            if ($transaction->type === 'expense') {
                $this->updateBudgetConsumption($transaction);
            }
        });
    }

    /**
     * Transfer money between two accounts of the same user (BR-FIN-003).
     */
    public function transfer(int $fromAccountId, int $toAccountId, float $amount, string $description = null, $date = null): Transaction
    {
        $from = Account::forUser()->findOrFail($fromAccountId);
        $to = Account::forUser()->findOrFail($toAccountId);

        if ($from->id === $to->id) {
            throw new \InvalidArgumentException('لا يمكن التحويل إلى نفس الحساب');
        }

        if ($amount <= 0) {
            throw new \InvalidArgumentException('مبلغ التحويل يجب أن يكون أكبر من صفر');
        }

        if ((float) $from->balance < $amount) {
            throw new \DomainException('رصيد الحساب المصدر غير كافٍ');
        }

        return $this->create([
            'user_id' => $from->user_id,
            'account_id' => $from->id,
            'category_id' => null,
            'type' => 'transfer',
            'amount' => $amount,
            'description' => $description,
            'transaction_date' => $date ?? now()->toDateString(),
            'transfer_to_account_id' => $to->id,
        ]);
    }

    /**
     * Create today's recurring transactions (BR-FIN-004). Idempotent.
     */
    public function processRecurring(): int
    {
        $created = 0;

        Transaction::query()
            ->where('is_recurring', true)
            ->where(function ($query) {
                $query->whereNull('recurrence_end_date')
                    ->orWhere('recurrence_end_date', '>=', now()->toDateString());
            })
            ->chunkById(200, function ($templates) use (&$created) {
                foreach ($templates as $template) {
                    if (! $this->alreadyProcessedToday($template)) {
                        $this->create([
                            'user_id' => $template->user_id,
                            'account_id' => $template->account_id,
                            'category_id' => $template->category_id,
                            'type' => $template->type,
                            'amount' => $template->amount,
                            'description' => $template->description,
                            'transaction_date' => now()->toDateString(),
                            'is_recurring' => false,
                            'transfer_to_account_id' => $template->transfer_to_account_id,
                        ]);
                        $created++;
                    }
                }
            });

        return $created;
    }

    /**
     * Recalculate an account balance from its transactions (BR-FIN-002, 003).
     */
    public function recalculateAccount(Account $account): void
    {
        DB::transaction(function () use ($account) {
            $income = $account->transactions()
                ->income()
                ->whereNull('deleted_at')
                ->sum('amount');

            $expense = $account->transactions()
                ->expense()
                ->whereNull('deleted_at')
                ->sum('amount');

            $outgoing = Transaction::query()
                ->where('account_id', $account->id)
                ->where('type', 'transfer')
                ->whereNull('deleted_at')
                ->sum('amount');

            $incoming = Transaction::query()
                ->where('transfer_to_account_id', $account->id)
                ->where('type', 'transfer')
                ->whereNull('deleted_at')
                ->sum('amount');

            $balance = (float) $account->initial_balance + (float) $income - (float) $expense - (float) $outgoing + (float) $incoming;

            $account->update(['balance' => max($balance, 0)]);
        });
    }

    /**
     * Update budget consumption for a transaction (BR-FIN-007, 008).
     */
    public function updateBudgetConsumption(Transaction $transaction): void
    {
        if (! $transaction->category_id || $transaction->type === 'transfer') {
            return;
        }

        Budget::query()
            ->forUser($transaction->user_id)
            ->active()
            ->where('start_date', '<=', $transaction->transaction_date)
            ->where('end_date', '>=', $transaction->transaction_date)
            ->get()
            ->each(function (Budget $budget) use ($transaction) {
                $category = $budget->budgetCategories()
                    ->where('category_id', $transaction->category_id)
                    ->first();

                $isTotalThreshold = $this->budgetService->checkAlert($budget, $transaction);
                $isCategoryThreshold = $category
                    ? $this->budgetService->checkCategoryAlert($budget, $category, $transaction)
                    : false;

                if ($isTotalThreshold || $isCategoryThreshold) {
                    BudgetThresholdReached::dispatch($budget, $transaction);
                }
            });
    }

    private function recalculateAccounts(Transaction $transaction): void
    {
        if ($transaction->type === 'transfer') {
            $this->recalculateAccount($transaction->account);
            if ($transaction->transferToAccount) {
                $this->recalculateAccount($transaction->transferToAccount);
            }
        } else {
            $this->recalculateAccount($transaction->account);
        }
    }

    private function alreadyProcessedToday(Transaction $template): bool
    {
        return Transaction::query()
            ->where('user_id', $template->user_id)
            ->where('account_id', $template->account_id)
            ->where('amount', $template->amount)
            ->where('type', $template->type)
            ->where('transaction_date', now()->toDateString())
            ->where('is_recurring', false)
            ->whereNull('deleted_at')
            ->exists();
    }
}
