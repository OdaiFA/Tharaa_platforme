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

        $financialActivity = Transaction::query()
            ->whereNull('deleted_at')
            ->selectRaw('DATE_FORMAT(transaction_date, "%Y-%m") as month, type, SUM(amount) as total')
            ->groupBy('month', 'type')
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

        $topCategories = DB::table('transactions')
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->where('transactions.type', 'expense')
            ->whereNull('transactions.deleted_at')
            ->selectRaw('categories.name, SUM(transactions.amount) as total')
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $months = $financialActivity->pluck('month')->unique()->values();
        $incomeByMonth = $financialActivity->where('type', 'income')->pluck('total', 'month');
        $expenseByMonth = $financialActivity->where('type', 'expense')->pluck('total', 'month');

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
                'income' => $months->map(fn ($month) => (float) ($incomeByMonth[$month] ?? 0)),
                'expense' => $months->map(fn ($month) => (float) ($expenseByMonth[$month] ?? 0)),
            ],
            'topCategories' => [
                'labels' => $topCategories->pluck('name'),
                'data' => $topCategories->pluck('total'),
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
