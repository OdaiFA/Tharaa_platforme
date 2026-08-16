<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function edit(): View
    {
        return view('profile.edit', [
            'user' => auth()->user()->load('settings'),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->safe()->except(['avatar']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        $this->authService->assignAgeGroup($user);

        return back()->with('success', 'تم تحديث الملف الشخصي بنجاح');
    }
}
