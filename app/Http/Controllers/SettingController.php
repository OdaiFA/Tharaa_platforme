<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('settings.index', [
            'settings' => auth()->user()->settings()->firstOrNew(),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $user = auth()->user();
        $data = $request->validated();

        if ($user->settings()->exists()) {
            $user->settings()->update($data);
        } else {
            $user->settings()->create(array_merge($data, [
                'user_id' => $user->id,
            ]));
        }

        return back()->with('success', 'تم تحديث الإعدادات بنجاح');
    }
}
