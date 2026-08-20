<?php

namespace App\Livewire\Budgets;

use App\Repositories\BudgetRepository;
use App\Services\BudgetService;
use Livewire\Component;

class BudgetsIndex extends Component
{
    public function render(BudgetRepository $budgets, BudgetService $budgetService)
    {
        $items = $budgets->activeForUser(auth()->id())
            ->map(fn ($budget) => [
                'budget' => $budget,
                'consumption' => $budgetService->calculateConsumption($budget),
            ]);

        return view('livewire.budgets.budgets-index', [
            'budgets' => $items,
        ]);
    }
}
