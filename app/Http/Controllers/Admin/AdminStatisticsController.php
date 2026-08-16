<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminStatisticsController extends Controller
{
    public function __invoke(): View
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

        $quizStats = [
            'attempts' => QuizAttempt::count(),
            'passed' => QuizAttempt::where('is_passed', true)->count(),
            'pass_rate' => QuizAttempt::count() > 0
                ? round((QuizAttempt::where('is_passed', true)->count() / QuizAttempt::count()) * 100, 1)
                : 0,
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

        return view('admin.statistics', compact(
            'usersByRole',
            'enrollmentsByCourse',
            'financialActivity',
            'enrollmentsByStatus',
            'quizStats',
            'topCategories',
        ));
    }
}
