<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PreorderStatusMail;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderDetail;
use App\Models\Preorder;
use App\Models\PreorderProduct;
use DB;

use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class PreorderManagementController extends Controller
{
    // Pre-Order Management Dashboard/Index
      public function index(Request $request)
{
    // --- নতুন এবং উন্নত পরিসংখ্যান গণনা ---
    $stats = [
        'total_preorder_products' => \App\Models\Product::where('is_preorder', 1)->count(),
        'total_preorders'         => \App\Models\Preorder::distinct('order_code')->count(),
        'total_customers'         => \App\Models\Preorder::distinct('shipping_email')->count(),
        
        'total_revenue'           => \App\Models\Preorder::sum('grand_total'),
        'total_prepayment_received' => \App\Models\Preorder::whereIn('status', ['confirmed', 'shipped', 'completed'])->sum('prepayment'),
        'total_remaining_due'     => \App\Models\Preorder::whereIn('status', ['confirmed', 'shipped', 'completed'])->sum(\DB::raw('grand_total - prepayment')),
        
        'pending_orders'          => \App\Models\Preorder::whereIn('status', ['pending_payment', 'payment_processing'])->distinct('order_code')->count(),
        'confirmed_orders'        => \App\Models\Preorder::where('status', 'confirmed')->distinct('order_code')->count(),
        'shipped_orders'          => \App\Models\Preorder::where('status', 'shipped')->distinct('order_code')->count(),
        'completed_orders'        => \App\Models\Preorder::where('status', 'completed')->distinct('order_code')->count(),
        'cancelled_orders'        => \App\Models\Preorder::where('status', 'cancelled')->distinct('order_code')->count(),
    ];
    // --- পরিসংখ্যান গণনা শেষ ---

    $all_grouped_orders = Preorder::with(['user'])
        ->latest()
        ->get()
        ->groupBy('order_code');

    $perPage = 20;
    $currentPage = Paginator::resolveCurrentPage('page');
    $currentPageItems = $all_grouped_orders->slice(($currentPage - 1) * $perPage, $perPage)->all();

    $recent_preorders = new LengthAwarePaginator(
        $currentPageItems,
        count($all_grouped_orders),
        $perPage,
        $currentPage,
        ['path' => Paginator::resolveCurrentPath()]
    );

    return view('backend.preorder_management.index', compact('stats', 'recent_preorders'));
}


     private function getDefaultEmailMessage($status, $order)
    {
        $remaining_amount = $order->grand_total - $order->prepayment;
        switch ($status) {
            case 'confirmed':
                return translate('Dear Customer, Your pre-order #') . $order->order_code . translate(' has been confirmed. We estimate it will be ready for delivery in approximately 20-30 days. We will notify you again once it is shipped.');
            case 'shipped':
                return translate('Great news! Your pre-order #') . $order->order_code . translate(' has been shipped. If there is any remaining balance of ') . single_price($remaining_amount) . translate(', please be prepared to pay it to the delivery person.');
            case 'completed':
                return translate('Your pre-order #') . $order->order_code . translate(' has been successfully delivered. We hope you enjoy your product!');
            case 'cancelled':
                return translate('We are sorry to inform you that your pre-order #') . $order->order_code . translate(' has been cancelled. Please contact us for further details.');
            default:
                return translate('Your pre-order status has been updated.');
        }
    }

    // All Pre-Order Products Management
     public function allProducts(Request $request)
    {
        $sort_search = null;
        $products_query = Product::where('is_preorder', 1)->latest();

        if ($request->has('search')) {
            $sort_search = $request->search;
            $products_query->where('name', 'like', '%'.$sort_search.'%');
        }

        $products = $products_query->paginate(15);
        return view('backend.preorder_management.products.index', compact('products', 'sort_search'));
    }

    public function updateProductStatus(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->is_preorder = $request->status;
        $product->save();

        return 1; // Return 1 for success to match the JavaScript expectation
    }

    public function updatePreorderStatus(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->is_preorder = $request->status;
        $product->save();

        return response()->json(['success' => true, 'message' => 'Pre-order status updated successfully']);
    }

    public function updatePublishedStatus(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->published = $request->status;
        $product->save();

        return response()->json(['success' => true, 'message' => 'Published status updated successfully']);
    }

    public function updateFeaturedStatus(Request $request)
    {
        $product = Product::findOrFail($request->id);
        $product->featured = $request->status;
        $product->save();

        return response()->json(['success' => true, 'message' => 'Featured status updated successfully']);
    }

    public function updatePreorderPrice(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
            'price' => 'required|numeric|min:0'
        ]);

        $product = Product::findOrFail($request->id);
        $product->preorder_price = $request->price;
        $product->save();

        return response()->json([
            'success' => true, 
            'message' => 'Pre-order price updated successfully',
            'formatted_price' => single_price($product->preorder_price)
        ]);
    }

    // Pre-Order Customers Management
    public function customers(Request $request)
    {
        $sort_search = null;
        $user_type = $request->get('user_type', 'all'); // all, guest, customer, wholesaler
        
        // Base query for all pre-orders
        $baseQuery = Preorder::query();

        // Filter by user type
        if ($user_type == 'guest') {
            $baseQuery->whereNull('user_id');
        } elseif ($user_type == 'customer') {
            $baseQuery->whereHas('user', function($query) {
                $query->where('user_type', 'customer');
            });
        } elseif ($user_type == 'wholesaler') {
            $baseQuery->whereHas('user', function($query) {
                $query->where('user_type', 'seller')
                      ->whereHas('shop', function($shopQuery) {
                          $shopQuery->where('verification_status', 1);
                      });
            });
        }

        // Search functionality
        if ($request->has('search')) {
            $sort_search = $request->search;
            $baseQuery->where(function($query) use ($sort_search) {
                $query->where('order_code', 'like', '%'.$sort_search.'%')
                      ->orWhereHas('user', function($userQuery) use ($sort_search) {
                          $userQuery->where('name', 'like', '%'.$sort_search.'%')
                                   ->orWhere('email', 'like', '%'.$sort_search.'%')
                                   ->orWhere('phone', 'like', '%'.$sort_search.'%');
                      })
                      ->orWhere('shipping_address', 'like', '%'.$sort_search.'%');
            });
        }

        // Get orders with comprehensive data
        $preorders = $baseQuery->with([
            'user', 
            'product'
        ])
        ->select([
            'id', 'order_code', 'user_id', 'grand_total', 'prepayment', 'request_preorder_status',
            'delivery_status', 'delivery_time', 'shipping_address',
            'created_at', 'confirmed_at', 'product_arrived_at', 'completed_at', 'quantity'
        ])
        ->latest()
        ->paginate(15);

        // Calculate statistics for each order
        foreach ($preorders as $order) {
            $order->remaining_amount = $order->grand_total - ($order->prepayment ?? 0);
            $order->payment_percentage = $order->grand_total > 0 ? (($order->prepayment ?? 0) / $order->grand_total) * 100 : 0;
            $order->days_since_order = $order->created_at->diffInDays(now());
            
            // Calculate estimated delivery days
            if ($order->delivery_date) {
                $order->days_to_delivery = now()->diffInDays($order->delivery_date, false);
            } else {
                $order->days_to_delivery = null;
            }

            // Get customer type
            if (!$order->user_id) {
                $order->customer_type = 'Guest';
                $order->customer_name = json_decode($order->shipping_address)->name ?? 'Guest Customer';
                $order->customer_email = json_decode($order->shipping_address)->email ?? 'N/A';
            } else {
                $user = $order->user;
                if ($user->user_type == 'seller' && $user->shop && $user->shop->verification_status == 1) {
                    $order->customer_type = 'Wholesaler';
                    $order->customer_name = $user->shop->name ?? $user->name;
                } else {
                    $order->customer_type = 'Customer';
                    $order->customer_name = $user->name;
                }
                $order->customer_email = $user->email;
            }

            // Count products in this pre-order (single product per preorder)
            $order->product_count = 1;
            $order->total_quantity = $order->quantity;
        }

        // Summary statistics
        $stats = [
            'total_preorders' => Preorder::count(),
            'total_amount' => Preorder::sum('grand_total'),
           'total_paid' => Preorder::sum('prepayment'), 
        'pending_amount' => Preorder::sum(\DB::raw('grand_total - prepayment')), 
            'guest_orders' => Preorder::whereNull('user_id')->count(),
            'customer_orders' => Preorder::whereHas('user', function($query) {
                $query->where('user_type', 'customer');
            })->count(),
            'wholesaler_orders' => Preorder::whereHas('user', function($query) {
                $query->where('user_type', 'seller')
                      ->whereHas('shop', function($shopQuery) {
                          $shopQuery->where('verification_status', 1);
                      });
            })->count(),
        ];

        return view('backend.preorder_management.customers.index', compact('preorders', 'sort_search', 'user_type', 'stats'));
    }

    public function dashboard()
    {
        return redirect()->route('preorder_management.customers');
    }

    public function customerDetails($id)
    {
        $order = Preorder::with(['user', 'product'])->findOrFail($id);
        return view('backend.preorder_management.customers.details', compact('order'));
    }

    public function updatePayment(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:preorders,id',
            'payment_amount' => 'required|numeric|min:0',
            'payment_notes' => 'nullable|string'
        ]);

        $order = Preorder::findOrFail($request->order_id);
        $newPaidAmount = $order->paid_amount + $request->payment_amount;
        
        // Ensure we don't exceed the grand total
        if ($newPaidAmount > $order->grand_total) {
            $newPaidAmount = $order->grand_total;
        }

        $order->update([
            'paid_amount' => $newPaidAmount,
            'preorder_notes' => $order->preorder_notes . "\n" . now()->format('Y-m-d H:i:s') . " - Payment Update: " . $request->payment_notes
        ]);

        flash(translate('Payment updated successfully'))->success();
        return redirect()->back();
    }

    public function updateDelivery(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:preorders,id',
            'delivery_date' => 'nullable|date',
            'delivery_location' => 'nullable|string',
            'delivery_notes' => 'nullable|string'
        ]);

        $order = Preorder::findOrFail($request->order_id);
        
        $updateData = [];
        if ($request->delivery_date) {
            $updateData['delivery_date'] = $request->delivery_date;
        }
        if ($request->delivery_location) {
            $updateData['delivery_location'] = $request->delivery_location;
        }
        if ($request->delivery_notes) {
            $updateData['delivery_notes'] = $request->delivery_notes;
        }

        $order->update($updateData);

        flash(translate('Delivery information updated successfully'))->success();
        return redirect()->back();
    }

   public function show($id)
{
    $order_first_item = Preorder::findOrFail($id);
    Preorder::where('order_code', $order_first_item->order_code)->update(['is_viewed' => 1]);
    return view('preorder.backend.orders.show', ['order' => $order_first_item]);
}

public function updateStatus(Request $request, $order_code)
{
    $request->validate([
        'status' => 'required|string|in:confirmed,shipped,completed,cancelled',
        'notify_customer' => 'nullable',
        'email_message' => 'nullable|string'
    ]);

    $preorders = \App\Models\Preorder::where('order_code', $order_code)->get();
    if ($preorders->isEmpty()) {
        flash(translate('Order not found!'))->error();
        return back();
    }
    
    $order_instance = $preorders->first();
    $order_instance->status = $request->status;

    \App\Models\Preorder::where('order_code', $order_code)->update(['status' => $request->status]);

    if ($request->notify_customer == '1') {
        
        // --- নতুন এবং চূড়ান্ত ইমেইল খোঁজার লজিক ---
        $customer_email = null;
        if ($order_instance->user && $order_instance->user->email) {
            // যদি রেজিস্টার্ড ব্যবহারকারী হয়, তাহলে user টেবিল থেকে ইমেইল নেওয়া হচ্ছে
            $customer_email = $order_instance->user->email;
        } elseif ($order_instance->shipping_email) {
            // যদি গেস্ট হয়, তাহলে shipping_email ফিল্ড থেকে নেওয়া হচ্ছে
            $customer_email = $order_instance->shipping_email;
        }
        // ---------------------------------------------

        if (!$customer_email || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
            flash(translate('Status updated, but failed to send notification because the customer email is missing or invalid.'))->warning();
            return back();
        }

        $status_message = $request->email_message ?: $this->getDefaultEmailMessage($request->status, $order_instance);
        
        try {
            Mail::to($customer_email)->send(new \App\Mail\PreorderStatusMail($order_instance, $status_message));
        } catch (\Exception $e) {
            flash(translate('Status updated, but failed to send notification email: ') . $e->getMessage())->warning();
            return back();
        }
    }

    flash(translate('Pre-order status has been updated successfully.'))->success();
    return back();
}



    public function getCustomerDetails(Request $request)
    {
        $customer = User::findOrFail($request->customer_id);
        
        $preorders = Preorder::where('user_id', $customer->id)
            ->with(['product'])
            ->latest()
            ->get();

        return response()->json([
            'success' => true, 
            'customer' => $customer,
            'preorders' => $preorders
        ]);
    }

    public function updateCustomerStatus(Request $request)
    {
        $customer = User::findOrFail($request->id);
        $customer->email_verified_at = $request->status == 1 ? now() : null;
        $customer->save();

        return response()->json(['success' => true, 'message' => 'Customer status updated successfully']);
    }

    // Pre-Order Payment Advance Management
    public function paymentAdvance(Request $request)
{
    $sort_search = $request->search;
    $status = $request->status; // স্ট্যাটাস ফিল্টার করার জন্য নতুন ভেরিয়েবল

    $orders_query = Preorder::groupBy('order_code')
        ->selectRaw('*, SUM(grand_total) as total_grand, SUM(prepayment) as total_prepayment')
        ->latest();

    if ($sort_search) {
        $orders_query->where(function ($q) use ($sort_search) {
            $q->where('order_code', 'like', '%' . $sort_search . '%')
              ->orWhere('shipping_name', 'like', '%' . $sort_search . '%');
        });
    }

    // নতুন ফিল্টার: স্ট্যাটাস অনুযায়ী অর্ডার দেখানো
    if ($status) {
        // 'incomplete' একটি কাল্পনিক স্ট্যাটাস যা আমরা অসম্পূর্ণ অর্ডার বোঝাতে ব্যবহার করব
        if ($status == 'incomplete') {
            $orders_query->where(function ($q) {
                $q->whereNull('shipping_name')
                  ->orWhereNull('shipping_email')
                  ->orWhereNull('shipping_phone')
                  ->orWhereNull('shipping_address');
            });
        } else {
            $orders_query->where('status', $status);
        }
    }

    $orders = $orders_query->paginate(15);

    return view('backend.preorder_management.payments.advance', compact('orders', 'sort_search', 'status'));
}

    public function processAdvancePayment(Request $request)
    {
        $order = Preorder::findOrFail($request->order_id);
        $order->payment_status = $request->payment_status;
        $order->advance_payment_amount = $request->advance_amount;
        $order->payment_notes = $request->payment_notes;
        $order->save();

        return response()->json(['success' => true, 'message' => 'Advance payment processed successfully']);
    }

    // Delivery Schedule & Balance Management
    public function deliverySchedule(Request $request)
    {
        $sort_search = null;
        
        $orders = Preorder::query();

        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('order_code', 'like', '%'.$sort_search.'%');
        }

        // Apply filters
        if ($request->has('delivery_status') && $request->delivery_status != '') {
            $orders = $orders->where('delivery_status', $request->delivery_status);
        }

        if ($request->has('date_from') && $request->date_from != '') {
            $orders = $orders->whereDate('delivery_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to != '') {
            $orders = $orders->whereDate('delivery_date', '<=', $request->date_to);
        }

        $orders = $orders->with(['user', 'product'])->latest()->paginate(15);

        // Calculate statistics
        $stats = [
            'pending_delivery' => Preorder::where('delivery_status', 0)->count(),
            'shipped_orders' => Preorder::where('delivery_status', 1)->count(),
            'delivered_orders' => Preorder::where('delivery_status', 2)->count(),
            'scheduled_today' => Preorder::whereDate('delivery_time', today())->count(),
            'overdue' => Preorder::where('delivery_time', '<', now())->where('delivery_status', '!=', 2)->count(),
            'total_orders' => Preorder::count()
        ];

        return view('backend.preorder_management.delivery.schedule', compact('orders', 'sort_search', 'stats'));
    }

    public function updateDeliverySchedule(Request $request)
    {
        $order = Preorder::findOrFail($request->order_id);
        $order->delivery_date = $request->delivery_date;
        $order->delivery_status = $request->delivery_status;
        $order->delivery_notes = $request->delivery_notes;
        $order->save();

        // Send comprehensive notifications for delivery schedule update
        $this->sendDeliveryNotifications($order, 'schedule_updated');

        return response()->json(['success' => true, 'message' => 'Delivery schedule updated successfully']);
    }

    public function markAsDelivered(Request $request)
    {
        $order = Preorder::findOrFail($request->order_id);
        $order->delivery_status = 'delivered';
        $order->delivered_at = now();
        $order->save();

        // Send comprehensive notifications for delivery completion
        $this->sendDeliveryNotifications($order, 'delivered');

        return response()->json(['success' => true, 'message' => 'Order marked as delivered successfully']);
    }

     public function destroy_preorder($order_code)
    {
        // আপনি চাইলে এখানে পারমিশন চেক যোগ করতে পারেন
        // if(auth()->user()->can('delete_preorder')) { ... }

        $preorders = Preorder::where('order_code', $order_code)->get();

        if($preorders->isEmpty()){
            flash(translate('Order not found!'))->error();
            return back();
        }

        Preorder::where('order_code', $order_code)->delete();
        flash(translate('Pre-order has been deleted successfully.'))->success();
        
        return back();
    }
    private function sendDeliveryNotifications($order, $status)
    {
        try {
            // Determine user type for personalized messaging
            $userType = $this->getUserType($order);
            
            // Send Email Notification
            $this->sendDeliveryEmail($order, $status, $userType);
            
            // Send SMS Notification if enabled
            $this->sendDeliverySMS($order, $status, $userType);
            
            // Send In-App Notification (for registered users)
            if ($order->user_id) {
                \App\Utility\NotificationUtility::sendNotification($order, $status);
            }
            
            // Send Firebase Notification (for registered users with device tokens)
            if ($order->user_id && get_setting('google_firebase') == 1 && $order->user->device_token != null) {
                $this->sendFirebaseDeliveryNotification($order, $status, $userType);
            }
            
        } catch (\Exception $e) {
            // Log error but don't break the flow
            \Log::error('Delivery notification error: ' . $e->getMessage());
        }
    }

    /**
     * Determine user type (Guest/Customer/Wholesaler)
     */
    private function getUserType($order)
    {
        if (!$order->user_id) {
            return 'Guest';
        }
        
        if ($order->user->user_type === 'wholesaler') {
            return 'Wholesaler';
        }
        
        return 'Customer';
    }

    /**
     * Send delivery email notification
     */
    private function sendDeliveryEmail($order, $status, $userType)
    {
        try {
            $shipping = json_decode($order->shipping_address, true);
            $email = $shipping['email'] ?? ($order->user->email ?? null);
            
            if (!$email) return;
            
            $subject = $this->getEmailSubject($status, $userType, $order);
            $body = $this->getEmailBody($status, $userType, $order, $shipping);
            
            $array = [
                'subject' => $subject,
                'content' => $body
            ];
            
            \Mail::to($email)->queue(new \App\Mail\MailManager($array));
            
        } catch (\Exception $e) {
            \Log::error('Delivery email error: ' . $e->getMessage());
        }
    }

    /**
     * Send delivery SMS notification
     */
    private function sendDeliverySMS($order, $status, $userType)
    {
        try {
            if (addon_is_activated('otp_system')) {
                $shipping = json_decode($order->shipping_address, true);
                $phone = $shipping['phone'] ?? ($order->user->phone ?? null);
                
                if ($phone) {
                    $message = $this->getSMSMessage($status, $userType, $order);
                    // Use existing SMS utility or implement custom SMS sending
                    \App\Utility\SmsUtility::delivery_status_change($phone, $order);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Delivery SMS error: ' . $e->getMessage());
        }
    }

    /**
     * Send Firebase notification for delivery updates
     */
    private function sendFirebaseDeliveryNotification($order, $status, $userType)
    {
        try {
            $request = new \Illuminate\Http\Request();
            $request->device_token = $order->user->device_token;
            $request->title = $this->getFirebaseTitle($status, $userType);
            $request->text = $this->getFirebaseMessage($status, $userType, $order);
            $request->type = "delivery";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            \App\Utility\NotificationUtility::sendFirebaseNotification($request);
        } catch (\Exception $e) {
            \Log::error('Firebase delivery notification error: ' . $e->getMessage());
        }
    }

    /**
     * Get email subject based on status and user type
     */
    private function getEmailSubject($status, $userType, $order)
    {
        $subjects = [
            'schedule_updated' => [
                'Guest' => 'Delivery Schedule Updated - Order ' . $order->code,
                'Customer' => 'Your Delivery Schedule Has Been Updated - Order ' . $order->code,
                'Wholesaler' => 'Wholesale Order Delivery Schedule Updated - Order ' . $order->code
            ],
            'delivered' => [
                'Guest' => 'Order Delivered Successfully - Order ' . $order->code,
                'Customer' => 'Your Order Has Been Delivered - Order ' . $order->code,
                'Wholesaler' => 'Wholesale Order Delivered - Order ' . $order->code
            ]
        ];
        
        return $subjects[$status][$userType] ?? 'Delivery Update - Order ' . $order->code;
    }

    /**
     * Get email body based on status and user type
     */
    private function getEmailBody($status, $userType, $order, $shipping)
    {
        $customerName = $shipping['name'] ?? ($order->user->name ?? 'Valued Customer');
        $deliveryDate = $order->delivery_date ? date('F j, Y', strtotime($order->delivery_date)) : 'To be confirmed';
        $storeName = get_setting('site_name');
        
        if ($status === 'schedule_updated') {
            return "
                <p>Dear {$customerName},</p>
                <p>We hope this message finds you well. We're writing to inform you that the delivery schedule for your pre-order has been updated.</p>
                
                <div style='background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;'>
                    <h3 style='color: #333; margin-top: 0;'>Order Details:</h3>
                    <p><strong>Order Code:</strong> {$order->code}</p>
                    <p><strong>Customer Type:</strong> {$userType}</p>
                    <p><strong>New Delivery Date:</strong> {$deliveryDate}</p>
                    <p><strong>Delivery Status:</strong> " . ucfirst(str_replace('_', ' ', $order->delivery_status)) . "</p>
                    " . ($order->delivery_notes ? "<p><strong>Delivery Notes:</strong> {$order->delivery_notes}</p>" : "") . "
                </div>
                
                <p>We appreciate your patience and understanding. If you have any questions or concerns about your delivery, please don't hesitate to contact our customer support team.</p>
                
                <p>Thank you for choosing {$storeName}!</p>
                
                <p>Best regards,<br>
                The {$storeName} Team</p>
            ";
        } else {
            return "
                <p>Dear {$customerName},</p>
                <p>Great news! Your pre-order has been successfully delivered.</p>
                
                <div style='background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #28a745;'>
                    <h3 style='color: #155724; margin-top: 0;'>Delivery Confirmation:</h3>
                    <p><strong>Order Code:</strong> {$order->code}</p>
                    <p><strong>Customer Type:</strong> {$userType}</p>
                    <p><strong>Delivered On:</strong> " . date('F j, Y g:i A') . "</p>
                </div>
                
                <p>We hope you're satisfied with your purchase. If you have any issues with your order, please contact us within 24 hours.</p>
                
                <p>Thank you for your business!</p>
                
                <p>Best regards,<br>
                The {$storeName} Team</p>
            ";
        }
    }

    /**
     * Get SMS message based on status and user type
     */
    private function getSMSMessage($status, $userType, $order)
    {
        $storeName = get_setting('site_name');
        
        if ($status === 'schedule_updated') {
            return "Hi! Your {$userType} pre-order {$order->code} delivery schedule has been updated. New delivery date: " . 
                   ($order->delivery_date ? date('M j, Y', strtotime($order->delivery_date)) : 'TBC') . 
                   ". Status: " . ucfirst($order->delivery_status) . ". - {$storeName}";
        } else {
            return "Great news! Your {$userType} pre-order {$order->code} has been delivered successfully. Thank you for choosing {$storeName}!";
        }
    }

    /**
     * Get Firebase notification title
     */
    private function getFirebaseTitle($status, $userType)
    {
        if ($status === 'schedule_updated') {
            return "Delivery Schedule Updated";
        } else {
            return "Order Delivered!";
        }
    }

    /**
     * Get Firebase notification message
     */
    private function getFirebaseMessage($status, $userType, $order)
    {
        if ($status === 'schedule_updated') {
            return "Your {$userType} pre-order {$order->code} delivery schedule has been updated.";
        } else {
            return "Your {$userType} pre-order {$order->code} has been delivered successfully!";
        }
    }

    public function bulkUpdateDelivery(Request $request)
    {
        $orderIds = explode(',', $request->order_ids);
        
        Preorder::whereIn('id', $orderIds)->update([
            'delivery_date' => $request->bulk_delivery_date,
            'delivery_status' => $request->bulk_delivery_status,
            'updated_at' => now()
        ]);

        return response()->json(['success' => true, 'message' => 'Delivery schedules updated successfully']);
    }

    // Alias methods to match route names
    public function allPreorderProducts(Request $request)
    {
        return $this->allProducts($request);
    }

    public function preorderCustomers(Request $request)
    {
        return $this->customers($request);
    }

    public function preorderPaymentsAdvance(Request $request)
    {
        return $this->paymentAdvance($request);
    }

    public function deliveryScheduleBalance(Request $request)
    {
        return $this->deliverySchedule($request);
    }

    // Pre-order Product Management - Flash Deal Style
    public function preorderProductManagement(Request $request)
    {
        $sort_search = null;
        $products = Product::isApprovedPublished()->where('auction_product', 0);
        
        if ($request->has('search')) {
            $sort_search = $request->search;
            $products = $products->where('name', 'like', '%'.$sort_search.'%');
        }
        
        $products = $products->orderBy('created_at', 'desc')->paginate(15);
        
        // Get pre-order products count
        $preorder_products_count = Product::where('is_preorder', 1)->count();
        
        return view('backend.preorder_management.product_management.index', compact('products', 'sort_search', 'preorder_products_count'));
    }

    public function updatePreorderProduct(Request $request)
    {
        $product = Product::findOrFail($request->product_id);
        
        if ($request->is_preorder == 1) {
            $product->is_preorder = 1;
            // If preorder_price is empty or 0, set to null for auto calculation
            $product->preorder_price = !empty($request->preorder_price) && $request->preorder_price > 0 
                ? $request->preorder_price 
                : null;
            $product->preorder_payment_percentage = $request->preorder_payment_percentage ?? 50;
        } else {
            $product->is_preorder = 0;
            $product->preorder_price = null;
            $product->preorder_payment_percentage = null;
        }
        
        $product->save();
        
        return response()->json([
            'success' => true,
            'message' => translate('Pre-order product updated successfully')
        ]);
    }

    public function getPreorderProducts(Request $request)
    {
        $products = Product::where('is_preorder', 1)
            ->with(['stocks', 'user'])
            ->orderBy('created_at', 'desc');
            
        if ($request->has('search')) {
            $products = $products->where('name', 'like', '%'.$request->search.'%');
        }
        
        $products = $products->paginate(15);
        
        return view('backend.preorder_management.product_management.preorder_list', compact('products'));
    }

    // Badge notification for pre-order payments
    public function getPreorderNotifications()
    {
        $pending_payments = Preorder::where('status', 'pending_payment')->count();
        $new_preorders = Preorder::where('created_at', '>=', now()->subHours(24))->count();
        
        return response()->json([
            'pending_payments' => $pending_payments,
            'new_preorders' => $new_preorders,
            'total_notifications' => $pending_payments + $new_preorders
        ]);
    }
}