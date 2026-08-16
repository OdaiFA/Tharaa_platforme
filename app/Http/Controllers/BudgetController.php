<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Models\Budget;
use App\Repositories\BudgetRepository;
use App\Services\BudgetService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function __construct(
        private readonly BudgetRepository $budgets,
        private readonly BudgetService $budgetService,
    ) {}

    public function index(): View
    {
        $budgets = $this->budgets->activeForUser(auth()->id())
            ->map(fn ($budget) => [
                'budget' => $budget,
                'consumption' => $this->budgetService->calculateConsumption($budget),
            ]);

        return view('budgets.index', compact('budgets'));
    }

    public function create(): View
    {
        $categories = $this->budgets->expenseCategories();

        return view('budgets.create', compact('categories'));
    }

    public function store(StoreBudgetRequest $request): RedirectResponse
    {
        $budget = $this->budgets->create(array_merge($request->safe()->except(['categories']), [
            'user_id' => auth()->id(),
        ]));

        foreach ($request->input('categories', []) as $category) {
            $budget->budgetCategories()->create([
                'category_id' => $category['category_id'],
                'limit_amount' => $category['limit_amount'],
                'alert_percentage' => $category['alert_percentage'] ?? 80,
            ]);
        }

        return redirect()->route('budgets.index')->with('success', 'تم إنشاء الميزانية بنجاح');
    }

    public function show(Budget $budget): View
    {
        $this->authorize('view', $budget);

        $consumption = $this->budgetService->calculateConsumption($budget);
        $categories = $this->budgetService->calculateCategoryConsumption($budget);

        return view('budgets.show', compact('budget', 'consumption', 'categories'));
    }

    public function edit(Budget $budget): View
    {
        $this->authorize('update', $budget);

        $categories = $this->budgets->expenseCategories();

        return view('budgets.edit', compact('budget', 'categories'));
    }

    public function update(UpdateBudgetRequest $request, Budget $budget): RedirectResponse
    {
        $this->authorize('update', $budget);

        $this->budgets->update($budget, $request->validated());

        return redirect()->route('budgets.index')->with('success', 'تم تحديث الميزانية بنجاح');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $this->authorize('delete', $budget);

        $this->budgets->delete($budget);

        return redirect()->route('budgets.index')->with('success', 'تم حذف الميزانية بنجاح');
    }
}
