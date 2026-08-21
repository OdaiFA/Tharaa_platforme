<?php

namespace App\Livewire\Notifications;

use App\Models\UserNotification;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationsIndex extends Component
{
    use WithPagination;

    public function markAsRead(string $notificationId)
    {
        $notification = UserNotification::findOrFail($notificationId);

        if ($notification->notifiable_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);

        if ($notification->action_url) {
            return redirect()->to($notification->action_url);
        }
    }

    public function markAllAsRead(): void
    {
        auth()->user()->userNotifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        session()->flash('success', 'تم تعليم جميع الإشعارات كمقروءة');
    }

    public function render()
    {
        return view('livewire.notifications.notifications-index', [
            'notifications' => auth()->user()->userNotifications()->latest()->paginate(15),
        ]);
    }
}
