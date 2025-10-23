<?php

namespace App\Utility;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PreorderNotification;

class PreorderNotificationUtility
{
    public static function preorderNotification($preorder, $statusType)
    {     
        $adminId = get_admin()->id;
        // Collect recipients: customer (if exists), product owner, and admin (when owner isn't admin)
        $userIds = [];
        if (!empty($preorder->user_id)) {
            $userIds[] = $preorder->user_id;
        }
        if (!empty($preorder->product_owner_id)) {
            $userIds[] = $preorder->product_owner_id;
        }
        if (!empty($adminId) && $preorder->product_owner_id != $adminId) {
            $userIds[] = $adminId;
        }
        // Remove duplicates
        $userIds = array_values(array_unique($userIds));
        $users = User::findMany($userIds);
        
        $order_notification = array();
        $order_notification['preorder_id'] = $preorder->id;
        $order_notification['order_code'] = $preorder->order_code;

        foreach($users as $user){
            $userType = in_array($user->user_type, ['admin','staff']) ? 'admin' : $user->user_type;
            $notificationType = get_notification_type('preorder_'.$statusType.'_'.$userType, 'type');
            if($notificationType != null && $notificationType->status == 1){
                $order_notification['notification_type_id'] = $notificationType->id;
                Notification::send($user, new PreorderNotification($order_notification));
            }
        }
    }
}
