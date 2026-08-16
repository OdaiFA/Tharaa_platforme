<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetRequest;
use App\Http\Requests\UpdateBudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiBudgetController extends Controller
{
    public function __construct(private readonly BudgetService $budgetService) {}

    public function index(Request $request): JsonResponse
    {
        $budgets = $request->user()->budgets()
            ->with('budgetCategories.category')
            ->paginate(15);

        return BudgetResource::collection($budgets);
    }

    public function store(StoreBudgetRequest $request): JsonResponse
    {
        $budget = $request->user()->budgets()->create($request->safe()->except(['categories']));

        foreach ($request->input('categories', []) as $category) {
            $budget->budgetCategories()->create($category);
        }

        return (new BudgetResource($budget->load('budgetCategories.category')))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, Budget $budget): JsonResponse
    {
        $this->authorize('view', $budget);

        $budget->load('budgetCategories.category');

        return response()->json([
            'budget' => new BudgetResource($budget),
            'consumption' => $this->budgetService->calculateConsumption($budget),
        ]);
    }

    public function update(UpdateBudgetRequest $request, Budget $budget): JsonResponse
    {
        $this->authorize('update', $budget);

        $budget->update($request->validated());

        return new BudgetResource($budget->fresh('budgetCategories.category'));
    }

    public function destroy(Request $request, Budget $budget): JsonResponse
    {
        $this->authorize('delete', $budget);

        $budget->delete();

        return response()->json(['message' => 'تم حذف الميزانية بنجاح']);
    }
}
