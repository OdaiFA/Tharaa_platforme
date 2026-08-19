<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        return view('admin.users.index');
    }

    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        DB::transaction(function () use ($request, $user) {
            $data = $request->validated();

            if (isset($data['is_active']) && ! $data['is_active'] && $user->id === auth()->id()) {
                abort(403, 'لا يمكنك تعطيل حسابك الخاص');
            }

            $user->update($data);
        });

        return back()->with('success', 'تم تحديث المستخدم بنجاح');
    }
}
