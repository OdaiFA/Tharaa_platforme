<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserNotificationResource;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = $request->user()->userNotifications()->latest()->paginate(20);

        return UserNotificationResource::collection($notifications);
    }

    public function markAsRead(Request $request, UserNotification $notification): JsonResponse
    {
        if ($notification->notifiable_id !== $request->user()->id) {
            abort(403);
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['message' => 'تم تعليم الإشعار كمقروء']);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->userNotifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return response()->json(['message' => 'تم تعليم جميع الإشعارات كمقروءة']);
    }
}
