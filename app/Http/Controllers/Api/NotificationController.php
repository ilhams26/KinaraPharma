<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')->get();
        return response()->json(['success' => true, 'data' => $notifications]);
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', auth()->id())->where('is_read', false)->count();
        return response()->json(['success' => true, 'unread_count' => $count]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notification->markAsRead();
        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true, 'message' => 'All marked as read']);
    }
}
