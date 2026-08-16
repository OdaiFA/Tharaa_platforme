<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetCategory;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class BudgetService
{
    /**
     * Calculate consumption for a budget: spent, remaining, percentage (BR-FIN-007).
     *
     * @return array{spent: float, remaining: float, percentage: int}
     */
    public function calculateConsumption(Budget $budget): array
    {
        $spent = (float) Transaction::query()
            ->forUser($budget->user_id)
            ->expense()
            ->whereBetween('transaction_date', [$budget->start_date, $budget->end_date])
            ->whereNull('deleted_at')
            ->sum('amount');

        $total = (float) $budget->total_amount;
        $remaining = max($total - $spent, 0);
        $percentage = $total > 0 ? (int) round(($spent / $total) * 100) : 0;

        return [
            'spent' => round($spent, 2),
            'remaining' => round($remaining, 2),
            'percentage' => min($percentage, 100),
        ];
    }

    /**
     * Calculate consumption per category within the budget.
     *
     * @return array<int, array>
     */
    public function calculateCategoryConsumption(Budget $budget): array
    {
        return $budget->budgetCategories()
            ->with('category')
            ->get()
            ->map(function (BudgetCategory $bc) use ($budget) {
                $spent = (float) Transaction::query()
                    ->forUser($budget->user_id)
                    ->expense()
                    ->where('category_id', $bc->category_id)
                    ->whereBetween('transaction_date', [$budget->start_date, $budget->end_date])
                    ->whereNull('deleted_at')
                    ->sum('amount');

                $limit = (float) $bc->limit_amount;
                $percentage = $limit > 0 ? (int) round(($spent / $limit) * 100) : 0;

                return [
                    'budget_category' => $bc,
                    'spent' => round($spent, 2),
                    'limit' => $limit,
                    'remaining' => round(max($limit - $spent, 0), 2),
                    'percentage' => min($percentage, 100),
                ];
            })
            ->all();
    }

    /**
     * Check if a transaction pushes total spending past the alert threshold (BR-NOT-003).
     */
    public function checkAlert(Budget $budget, ?Transaction $transaction = null): bool
    {
        $consumption = $this->calculateConsumption($budget);

        return $consumption['percentage'] >= $budget->alert_percentage;
    }

    /**
     * Check if a transaction pushes a category past its alert threshold.
     */
    public function checkCategoryAlert(Budget $budget, BudgetCategory $budgetCategory, ?Transaction $transaction = null): bool
    {
        $categoryConsumption = collect($this->calculateCategoryConsumption($budget))
            ->firstWhere('budget_category.id', $budgetCategory->id);

        if (! $categoryConsumption) {
            return false;
        }

        return $categoryConsumption['percentage'] >= $budgetCategory->alert_percentage;
    }
}
