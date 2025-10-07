<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    public function getNotifications(Request $request)
    {
        $adminId = $request->user()->admin_id;
        
        $notifications = \App\Models\Notification::with('requisition')
            ->where('admin_id', $adminId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $unreadCount = NotificationService::getUnreadCount($adminId);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead(Request $request, $notificationId = null)
    {
        $adminId = $request->user()->admin_id;
        
        NotificationService::markAsRead($adminId, $notificationId);

        $unreadCount = NotificationService::getUnreadCount($adminId);

        return response()->json([
            'success' => true,
            'unread_count' => $unreadCount
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $adminId = $request->user()->admin_id;
        
        NotificationService::markAsRead($adminId);

        return response()->json([
            'success' => true,
            'unread_count' => 0
        ]);
    }
}