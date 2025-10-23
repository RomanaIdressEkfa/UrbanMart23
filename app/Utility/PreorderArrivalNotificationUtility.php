<?php

namespace App\Utility;

use App\Models\User;
use App\Models\Preorder;
use App\Models\EmailTemplate;
use App\Mail\InvoiceEmailManager;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PreorderNotification;
use Mail;

class PreorderArrivalNotificationUtility
{
    // Mohammad Hassan
    public static function sendProductArrivalNotification($preorder)
    {
        // Send email notification to customer
        $customer = $preorder->user;
        $product = $preorder->product;
        
        if ($customer && $customer->email) {
            $emailTemplate = EmailTemplate::where('identifier', 'preorder_product_arrival')->first();
            
            if ($emailTemplate && $emailTemplate->status == 1) {
                $emailSubject = $emailTemplate->subject;
                $emailSubject = str_replace('[[product_name]]', $product->name, $emailSubject);
                $emailSubject = str_replace('[[order_code]]', $preorder->order_code, $emailSubject);
                
                $array = [
                    'view' => 'emails.preorder_arrival',
                    'subject' => $emailSubject,
                    'preorder' => $preorder,
                    'product' => $product,
                    'customer' => $customer
                ];
                
                try {
                    Mail::to($customer->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {
                    // Log error but continue
                }
            }
        }
        
        // Send in-app notification
        self::sendInAppNotification($preorder);
        
        // Send Firebase notification if enabled
        if (get_setting('google_firebase') == 1 && $customer->device_token != null) {
            self::sendFirebaseNotification($preorder);
        }
    }
    
    // Mohammad Hassan
    private static function sendInAppNotification($preorder)
    {
        $customer = $preorder->user;
        $product = $preorder->product;
        
        $notification_data = [
            'preorder_id' => $preorder->id,
            'order_code' => $preorder->order_code,
            'product_name' => $product->name,
            'status' => 'product_arrived'
        ];
        
        $notificationType = get_notification_type('preorder_product_arrival_customer', 'type');
        if ($notificationType && $notificationType->status == 1) {
            $notification_data['notification_type_id'] = $notificationType->id;
            Notification::send($customer, new PreorderNotification($notification_data));
        }
    }
    
    // Mohammad Hassan
    private static function sendFirebaseNotification($preorder)
    {
        $customer = $preorder->user;
        $product = $preorder->product;
        
        $url = 'https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send';
        
        $fields = [
            'to' => $customer->device_token,
            'notification' => [
                'body' => "Your pre-ordered product '{$product->name}' has arrived! Complete your payment to receive it.",
                'title' => 'Pre-order Product Available',
                'sound' => 'default'
            ],
            'data' => [
                'item_type' => 'preorder',
                'item_type_id' => $preorder->id,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        ];
        
        $headers = [
            'Authorization: key=' . env('FCM_SERVER_KEY'),
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        
        $result = curl_exec($ch);
        curl_close($ch);
        
        // Store Firebase notification record
        $firebase_notification = new \App\Models\FirebaseNotification;
        $firebase_notification->title = 'Pre-order Product Available';
        $firebase_notification->text = "Your pre-ordered product '{$product->name}' has arrived!";
        $firebase_notification->item_type = 'preorder';
        $firebase_notification->item_type_id = $preorder->id;
        $firebase_notification->receiver_id = $customer->id;
        $firebase_notification->save();
    }
}