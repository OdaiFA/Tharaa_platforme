<?php

namespace App\Http\Controllers;

use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        $notifications = auth()->user()->userNotifications()
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead(Request $request, UserNotification $notification): RedirectResponse
    {
        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return $notification->action_url
            ? redirect()->to($notification->action_url)
            : back();
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        auth()->user()->userNotifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return back()->with('success', 'تم تعليم جميع الإشعارات كمقروءة');
    }
}
