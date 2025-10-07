<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Admin;
use App\Models\RequisitionForm;

class NotificationService
{
    public static function notifyNewRequisition(RequisitionForm $requisition)
    {
        // Get all Head Admins (role_id 1), VPA (role_id 2), and Approving Officers (role_id 3)
        $admins = Admin::whereIn('role_id', [1, 2, 3])->get();

        $message = "New requisition submitted by {$requisition->first_name} {$requisition->last_name}";

        foreach ($admins as $admin) {
            Notification::create([
                'admin_id' => $admin->admin_id,
                'type' => 'new_requisition',
                'message' => $message,
                'request_id' => $requisition->request_id,
                'is_read' => false
            ]);
        }
    }

    public static function getUnreadCount($adminId)
    {
        return Notification::where('admin_id', $adminId)
            ->where('is_read', false)
            ->count();
    }

    public static function markAsRead($adminId, $notificationId = null)
    {
        if ($notificationId) {
            return Notification::where('admin_id', $adminId)
                ->where('notification_id', $notificationId)
                ->update(['is_read' => true]);
        }

        return Notification::where('admin_id', $adminId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}