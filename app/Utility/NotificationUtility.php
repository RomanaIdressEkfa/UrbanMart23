<?php

namespace App\Utility;

use App\Mail\InvoiceEmailManager;
use App\Models\User;
use App\Models\SmsTemplate;
use App\Http\Controllers\OTPVerificationController;
use App\Models\EmailTemplate;
use Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderNotification;
use App\Models\FirebaseNotification;

class NotificationUtility
{

    // app/Utility/NotificationUtility.php

public static function sendOrderPlacedNotification($order, $request = null)
{
    // --- START: সংশোধিত এবং সঠিক কোড ---
    
    $admin = get_admin();
    $seller = $order->shop ? $order->shop->user : null;
    $customer_email = null;

    if ($order->user_id) {
        // যদি রেজিস্টার্ড ব্যবহারকারী হন
        if ($order->user && $order->user->email) {
            $customer_email = $order->user->email;
        }
    } else {
        // যদি অতিথি ব্যবহারকারী হন, তাহলে shipping_address থেকে ইমেইল নেওয়া হচ্ছে
        $shipping_address = json_decode($order->shipping_address);
        if ($shipping_address && !empty($shipping_address->email)) {
            $customer_email = $shipping_address->email;
        }
    }

    // --- ইমেইল পাঠানোর জন্য প্রাপকদের তালিকা তৈরি করা ---
    $recipients = [];
    if ($admin && $admin->email) {
        $recipients[$admin->email] = 'admin';
    }
    if ($seller && $seller->email) {
        $recipients[$seller->email] = 'seller';
    }
    if ($customer_email) {
        $recipients[$customer_email] = 'customer';
    }
    
    foreach ($recipients as $email => $user_type) {
        $emailIdentifier = 'order_placed_email_to_' . $user_type;
        $emailTemplate = EmailTemplate::whereIdentifier($emailIdentifier)->first();

        if ($emailTemplate != null && $emailTemplate->status == 1) {
            $emailSubject = $emailTemplate->subject;
            $emailSubject = str_replace('[[order_code]]', $order->code, $emailSubject);

            $array['view'] = 'emails.invoice';
            $array['subject'] = $emailSubject;
            $array['order'] = $order;

            try {
                Mail::to($email)->queue(new InvoiceEmailManager($array));
            } catch (\Exception $e) {
                \Log::error('Order placed email sending failed for ' . $email . ': ' . $e->getMessage());
            }
        }
    }
    // --- END: সংশোধিত এবং সঠিক কোড ---

    // SMS পাঠানোর কোড (অপরিবর্তিত)
    if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'order_placement')->first()->status == 1) {
        try {
            (new OTPVerificationController)->send_order_code($order);
        } catch (\Exception $e) {
            \Log::error('Order placement SMS failed for order ' . $order->code . ': ' . $e->getMessage());
        }
    }

    // Web Notification পাঠানোর কোড
    self::sendNotification($order, 'placed');

    // Firebase Notification পাঠানোর কোড (যদি ব্যবহারকারী রেজিস্টার্ড হন)
    if ($request != null && get_setting('google_firebase') == 1 && $order->user_id && $order->user->device_token != null) {
        $request->device_token = $order->user->device_token;
        $request->title = "Order placed !";
        $request->text = "An order {$order->code} has been placed";
        $request->type = "order";
        $request->id = $order->id;
        $request->user_id = $order->user->id;

        self::sendFirebaseNotification($request);
    }
}
    // public static function sendOrderPlacedNotification($order, $request = null)
    // {       
    //     //sends email to Customer, Seller and Admin with the invoice pdf attached
    //     $adminId = get_admin()->id;
    //     $userIds = array($order->seller_id);
    //     if($order->user->email != null){
    //         array_push($userIds, $order->user_id);
    //     }
    //     if ($order->seller_id != $adminId) {
    //         array_push($userIds, $adminId);
    //     }
    //     $users = User::findMany($userIds);
    //     foreach($users as $user){
    //         $emailIdentifier = 'order_placed_email_to_'.$user->user_type;
    //         $emailTemplate = EmailTemplate::whereIdentifier($emailIdentifier)->first();

    //         if($emailTemplate != null && $emailTemplate->status == 1){
    //             $emailSubject = $emailTemplate->subject;
    //             $emailSubject = str_replace('[[order_code]]', $order->code, $emailSubject);

    //             $array['view']      = 'emails.invoice';
    //             $array['subject']   = $emailSubject;
    //             $array['order']     = $order;
    //             if($emailTemplate->status == 1){
    //                 try {
    //                     Mail::to($user->email)->queue(new InvoiceEmailManager($array));
    //                 } catch (\Exception $e) {}
    //             }
    //         }   
    //     }

    //     if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'order_placement')->first()->status == 1) {
    //         try {
    //             $otpController = new OTPVerificationController;
    //             $otpController->send_order_code($order);
    //         } catch (\Exception $e) {

    //         }
    //     }

    //     //sends Notifications to user
    //     self::sendNotification($order, 'placed');
    //     if ($request !=null && get_setting('google_firebase') == 1 && $order->user->device_token != null) {
    //         $request->device_token = $order->user->device_token;
    //         $request->title = "Order placed !";
    //         $request->text = "An order {$order->code} has been placed";

    //         $request->type = "order";
    //         $request->id = $order->id;
    //         $request->user_id = $order->user->id;

    //         self::sendFirebaseNotification($request);
    //     }
    // }

    // app/Utility/NotificationUtility.php

public static function sendNotification($order, $order_status)
{
    $adminId = get_admin()->id;
    $userIds = [];

    // --- START: সংশোধিত এবং সঠিক কোড ---
    
    // ১. প্রথমে সেলারকে যোগ করা হলো
    if ($order->seller_id) {
        $userIds[] = $order->seller_id;
    }

    // ২. রেজিস্টার্ড কাস্টমার হলে তাকে যোগ করা হলো
    if ($order->user_id) {
        $userIds[] = $order->user_id;
    }
    
    // ৩. অ্যাডমিনকে যোগ করা হলো (যদি সেলার থেকে ভিন্ন হন)
    if ($order->seller_id != $adminId) {
        $userIds[] = $adminId;
    }

    // ডুপ্লিকেট আইডি বাদ দেওয়া হলো
    $userIds = array_unique($userIds);

    if (empty($userIds)) {
        return; // যদি কোনো প্রাপক না থাকে, তাহলে ফাংশন থেকে বের হয়ে যাওয়া হবে
    }

    $users = User::findMany($userIds);

    $order_notification = array();
    $order_notification['order_id'] = $order->id;
    $order_notification['order_code'] = $order->code;
    $order_notification['user_id'] = $order->user_id; // এটি null থাকতে পারে, সমস্যা নেই
    $order_notification['seller_id'] = $order->seller_id;
    $order_notification['status'] = $order_status;

    foreach ($users as $user) {
        if ($user) { // নিশ্চিত করা হচ্ছে যে ইউজার অবজেক্টটি null নয়
            $notificationType = get_notification_type('order_' . $order_status . '_' . $user->user_type, 'type');
            if ($notificationType != null && $notificationType->status == 1) {
                $order_notification['notification_type_id'] = $notificationType->id;
                try {
                    Notification::send($user, new OrderNotification($order_notification));
                } catch (\Exception $e) {
                    \Log::error('Notification sending failed for user ' . $user->id . ': ' . $e->getMessage());
                }
            }
        }
    }
    // --- END: সংশোধিত এবং সঠিক কোড ---
}
    // public static function sendNotification($order, $order_status)
    // {     
    //     $adminId = get_admin()->id;
    //     $userIds = array($order->user->id, $order->seller_id);
    //     if ($order->seller_id != $adminId) {
    //         array_push($userIds, $adminId);
    //     }
    //     $users = User::findMany($userIds);
        
    //     $order_notification = array();
    //     $order_notification['order_id'] = $order->id;
    //     $order_notification['order_code'] = $order->code;
    //     $order_notification['user_id'] = $order->user_id;
    //     $order_notification['seller_id'] = $order->seller_id;
    //     $order_notification['status'] = $order_status;

    //     foreach($users as $user){
    //         $notificationType = get_notification_type('order_'.$order_status.'_'.$user->user_type, 'type');
    //         if($notificationType != null && $notificationType->status == 1){
    //             $order_notification['notification_type_id'] = $notificationType->id;
    //             Notification::send($user, new OrderNotification($order_notification));
    //         }
    //     }
    // }

    public static function sendFirebaseNotification($req)
    {        
        $url = 'https://fcm.googleapis.com/v1/projects/myproject-b5ae1/messages:send';

        $fields = array
        (
            'to' => $req->device_token,
            'notification' => [
                'body' => $req->text,
                'title' => $req->title,
                'sound' => 'default' /*Default sound*/
            ],
            'data' => [
                'item_type' => $req->type,
                'item_type_id' => $req->id,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        );

        //$fields = json_encode($arrayToSend);
        $headers = array(
            'Authorization: key=' . env('FCM_SERVER_KEY'),
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        curl_close($ch);

        $firebase_notification = new FirebaseNotification;
        $firebase_notification->title = $req->title;
        $firebase_notification->text = $req->text;
        $firebase_notification->item_type = $req->type;
        $firebase_notification->item_type_id = $req->id;
        $firebase_notification->receiver_id = $req->user_id;

        $firebase_notification->save();
    }
}
