<?php

namespace App\Livewire\Budgets;

use App\Models\Budget;
use App\Repositories\BudgetRepository;
use App\Services\BudgetService;
use Livewire\Component;

class BudgetShow extends Component
{
    public Budget $budget;

    public bool $confirmingDelete = false;

    public function mount(Budget $budget): void
    {
        $this->authorize('view', $budget);

        $this->budget = $budget;
    }

    public function confirmDelete(): void
    {
        $this->confirmingDelete = true;
    }

    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
    }

    public function delete(BudgetRepository $budgets)
    {
        $this->authorize('delete', $this->budget);

        $budgets->delete($this->budget);

        session()->flash('success', 'تم حذف الميزانية بنجاح');

        return redirect()->route('budgets.index');
    }

    public function render(BudgetService $budgetService)
    {
        return view('livewire.budgets.budget-show', [
            'consumption' => $budgetService->calculateConsumption($this->budget),
            'categories' => $budgetService->calculateCategoryConsumption($this->budget),
        ]);
    }
}
