<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContributeGoalRequest;
use App\Http\Requests\StoreGoalRequest;
use App\Http\Requests\UpdateGoalRequest;
use App\Models\Goal;
use App\Repositories\GoalRepository;
use App\Services\GoalService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GoalController extends Controller
{
    public function __construct(
        private readonly GoalRepository $goals,
        private readonly GoalService $goalService,
    ) {}

    public function index(): View
    {
        return view('goals.index');
    }

    public function create(): View
    {
        return view('goals.create');
    }

    public function store(StoreGoalRequest $request): RedirectResponse
    {
        $this->goals->create(array_merge($request->validated(), [
            'user_id' => auth()->id(),
        ]));

        return redirect()->route('goals.index')->with('success', 'تم إنشاء الهدف بنجاح');
    }

    public function contribute(ContributeGoalRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorize('contribute', $goal);

        try {
            $this->goalService->contribute(
                $goal,
                (float) $request->input('amount'),
                $request->input('account_id'),
                $request->input('note'),
                $request->input('contribution_date'),
            );
        } catch (\DomainException|\InvalidArgumentException $e) {
            return back()->withErrors(['amount' => $e->getMessage()]);
        }

        return redirect()->route('goals.index')->with('success', 'تمت إضافة المساهمة بنجاح');
    }

    public function edit(Goal $goal): View
    {
        $this->authorize('update', $goal);

        return view('goals.edit', compact('goal'));
    }

    public function update(UpdateGoalRequest $request, Goal $goal): RedirectResponse
    {
        $this->authorize('update', $goal);

        $this->goals->update($goal, $request->validated());

        return redirect()->route('goals.index')->with('success', 'تم تحديث الهدف بنجاح');
    }

    public function destroy(Goal $goal): RedirectResponse
    {
        $this->authorize('delete', $goal);

        $this->goals->delete($goal);

        return redirect()->route('goals.index')->with('success', 'تم حذف الهدف مع الاحتفاظ بسجل المساهمات');
    }
}
