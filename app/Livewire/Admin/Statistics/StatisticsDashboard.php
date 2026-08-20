<?php

namespace App\Livewire\Admin\Statistics;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class StatisticsDashboard extends Component
{
    public function render()
    {
        $usersByRole = User::selectRaw('role, COUNT(*) as total')->groupBy('role')->pluck('total', 'role');

        $enrollmentsByCourse = Course::query()
            ->withCount('enrollments')
            ->orderByDesc('enrollments_count')
            ->limit(10)
            ->get();

        // Transactions have no currency of their own — it's implied by the
        // owning account (same rule applied throughout the financial
        // module). Grouping by account currency alongside month/type keeps
        // a SAR total and a USD total from ever being summed into one
        // meaningless number.
        $financialActivity = Transaction::query()
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->whereNull('transactions.deleted_at')
            ->selectRaw('DATE_FORMAT(transactions.transaction_date, "%Y-%m") as month, transactions.type as type, accounts.currency as currency, SUM(transactions.amount) as total')
            ->groupBy('month', 'transactions.type', 'accounts.currency')
            ->orderBy('month')
            ->limit(24)
            ->get();

        $enrollmentsByStatus = Enrollment::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $attempts = QuizAttempt::count();
        $passed = QuizAttempt::where('is_passed', true)->count();
        $quizStats = [
            'attempts' => $attempts,
            'passed' => $passed,
            'pass_rate' => $attempts > 0 ? round(($passed / $attempts) * 100, 1) : 0,
        ];

        // Same rule for category spend: a category's SAR spend and USD
        // spend are two different rows, never combined.
        $topCategories = DB::table('transactions')
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->join('accounts', 'accounts.id', '=', 'transactions.account_id')
            ->where('transactions.type', 'expense')
            ->whereNull('transactions.deleted_at')
            ->selectRaw('categories.name as name, accounts.currency as currency, SUM(transactions.amount) as total')
            ->groupBy('categories.name', 'accounts.currency')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $months = $financialActivity->pluck('month')->unique()->sort()->values();
        $financialCurrencies = $financialActivity->pluck('currency')->unique()->sort()->values();

        $financialSeries = $financialCurrencies->map(function ($currency) use ($financialActivity, $months) {
            $income = $financialActivity->where('currency', $currency)->where('type', 'income')->pluck('total', 'month');
            $expense = $financialActivity->where('currency', $currency)->where('type', 'expense')->pluck('total', 'month');

            return [
                'currency' => $currency,
                'income' => $months->map(fn ($month) => (float) ($income[$month] ?? 0))->values(),
                'expense' => $months->map(fn ($month) => (float) ($expense[$month] ?? 0))->values(),
            ];
        })->values();

        $categoryLabels = $topCategories->pluck('name')->unique()->values();
        $categoryCurrencies = $topCategories->pluck('currency')->unique()->sort()->values();

        $categorySeries = $categoryCurrencies->map(function ($currency) use ($topCategories, $categoryLabels) {
            $byName = $topCategories->where('currency', $currency)->pluck('total', 'name');

            return [
                'currency' => $currency,
                'data' => $categoryLabels->map(fn ($name) => (float) ($byName[$name] ?? 0))->values(),
            ];
        })->values();

        $chartData = [
            'usersByRole' => [
                'labels' => $usersByRole->keys()->map(fn ($role) => $role === 'admin' ? 'مدير' : 'مستخدم')->values(),
                'data' => $usersByRole->values(),
            ],
            'enrollmentsByStatus' => [
                'labels' => $enrollmentsByStatus->keys(),
                'data' => $enrollmentsByStatus->values(),
            ],
            'financialActivity' => [
                'months' => $months,
                'series' => $financialSeries,
            ],
            'topCategories' => [
                'labels' => $categoryLabels,
                'series' => $categorySeries,
            ],
        ];

        return view('livewire.admin.statistics.statistics-dashboard', [
            'usersByRole' => $usersByRole,
            'enrollmentsByCourse' => $enrollmentsByCourse,
            'financialActivity' => $financialActivity,
            'enrollmentsByStatus' => $enrollmentsByStatus,
            'quizStats' => $quizStats,
            'topCategories' => $topCategories,
            'chartData' => $chartData,
        ]);
    }
}
