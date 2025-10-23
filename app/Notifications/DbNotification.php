<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DbNotification extends Notification
{
    public function send($notifiable, $notificationData)
    { 
        $notification_id= $notificationData->id;
        $contents       = $notificationData->data;
        $className      = $notificationData->className;
        $notifiableClass= new $notificationData->className($contents);
        $notifyData     = $notifiableClass->toArray($notifiable);

        $notificationTypeID = $notifyData['notification_type_id'];
        $data = $notifyData['data'];
        unset($notifyData);

        // Create the notification via the notifiable's database route.
        // The morph keys (notifiable_type, notifiable_id) are set automatically by the relation.
        return $notifiable->routeNotificationFor('database')->create([
            'id' => $notification_id,
            'notification_type_id' => $notificationTypeID,
            'type' => $className,
            'data' => $data,
            'read_at' => null,
        ]);
    }    

}
