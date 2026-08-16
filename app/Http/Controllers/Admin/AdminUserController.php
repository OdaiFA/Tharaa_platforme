<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->with('ageGroup')
            ->when($request->input('search'), fn ($q, $search) => $q->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            }))
            ->when($request->input('role'), fn ($q, $role) => $q->where('role', $role))
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load([
            'accounts',
            'budgets',
            'goals',
            'enrollments.course',
            'ageGroup',
        ]);

        $totals = [
            'balance' => (float) $user->accounts()->sum('balance'),
            'transactions' => $user->transactions()->count(),
            'enrollments' => $user->enrollments()->count(),
        ];

        return view('admin.users.show', compact('user', 'totals'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        DB::transaction(function () use ($request, $user) {
            $data = $request->validated();

            if (isset($data['is_active'])) {
                if (! $data['is_active'] && $user->id === auth()->id()) {
                    abort(403, 'لا يمكنك تعطيل حسابك الخاص');
                }

                $data['deleted_at'] = $data['is_active'] ? null : now();
                unset($data['is_active']);
            }

            $user->update($data);
        });

        return back()->with('success', 'تم تحديث المستخدم بنجاح');
    }
}
