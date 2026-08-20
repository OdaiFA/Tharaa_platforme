<?php

namespace App\Http\Controllers;

use App\Repositories\TransactionRepository;
use App\Repositories\AccountRepository;
use App\Repositories\GoalRepository;
use App\Services\BudgetService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly AccountRepository $accounts,
        private readonly TransactionRepository $transactions,
        private readonly GoalRepository $goals,
        private readonly BudgetService $budgetService,
    ) {}

    public function index(): View
    {
        $user = auth()->user();
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        // Grouped by currency, never summed across currencies — see
        // AccountRepository::totalBalanceForUser() / TransactionRepository::incomeForPeriod().
        $incomeByCurrency = $this->transactions->incomeForPeriod($user->id, $startOfMonth, $endOfMonth);
        $expenseByCurrency = $this->transactions->expenseForPeriod($user->id, $startOfMonth, $endOfMonth);
        $balanceByCurrency = $this->accounts->totalBalanceForUser($user->id);

        $recentTransactions = $this->transactions->recentForUser($user->id, 6);

        $enrollments = $user->enrollments()
            ->with('course')
            ->latest()
            ->take(4)
            ->get();

        $goals = $this->goals->activeForUser($user->id);
        $completedGoals = $this->goals->completedForUser($user->id);

        $learningProgress = $enrollments->isEmpty()
            ? 0
            : (int) round($enrollments->avg('progress_percentage'));

        $activeBudgets = $user->budgets()->active()->get()
            ->map(fn ($budget) => [
                'budget' => $budget,
                'consumption' => $this->budgetService->calculateConsumption($budget),
            ]);

        return view('dashboard.index', compact(
            'incomeByCurrency',
            'expenseByCurrency',
            'balanceByCurrency',
            'recentTransactions',
            'enrollments',
            'goals',
            'completedGoals',
            'learningProgress',
            'activeBudgets',
        ));
    }
}
