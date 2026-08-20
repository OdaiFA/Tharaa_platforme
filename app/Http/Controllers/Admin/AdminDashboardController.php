<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\TransactionRepository;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function __invoke(TransactionRepository $transactions): View
    {
        $stats = [
            'users' => User::count(),
            'new_users_this_month' => User::where('created_at', '>=', now()->startOfMonth())->count(),
            'courses' => Course::count(),
            'published_courses' => Course::where('is_published', true)->count(),
            'enrollments' => Enrollment::count(),
            'transactions' => Transaction::count(),
            // Grouped by currency, never summed across currencies — reuses
            // the same strategy as AccountRepository::totalBalanceForUser().
            'transaction_volume_by_currency' => $transactions->totalVolumeByCurrency(),
        ];

        $userGrowth = User::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->pluck('total', 'month');

        $recentUsers = User::latest()->take(8)->get();
        $recentEnrollments = Enrollment::with(['user', 'course'])->latest()->take(8)->get();

        return view('admin.dashboard', compact('stats', 'userGrowth', 'recentUsers', 'recentEnrollments'));
    }
}
