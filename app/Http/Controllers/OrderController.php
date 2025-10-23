<?php

namespace App\Http\Controllers;

use App\Http\Controllers\AffiliateController;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Preorder;
use App\Models\Cart;
use App\Models\Address;
use App\Models\Product;
use App\Models\OrderDetail;
use App\Models\CouponUsage;
use App\Models\Coupon;
use App\Models\User;
use App\Models\CombinedOrder;
use App\Models\SmsTemplate;
use Auth;
use Session; 
use Mail;
use App\Mail\InvoiceEmailManager;
use App\Models\OrdersExport;
use App\Utility\NotificationUtility;
use App\Utility\SmsUtility;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Notification;
use App\Notifications\OrderNotification;
use App\Utility\EmailUtility;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Exception;

class OrderController extends Controller
{

    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_all_orders|view_inhouse_orders|view_seller_orders|view_pickup_point_orders|view_all_offline_payment_orders'])->only('all_orders');
        $this->middleware(['permission:view_order_details'])->only('show');
        $this->middleware(['permission:delete_order'])->only('destroy','bulk_order_delete');
    }

    // All Orders
  // All Orders
    public function all_orders(Request $request)
    {
        // Mohammad Hassan - Removed CoreComponentRepository::instantiateShopRepository();

        $date = $request->date;
        $sort_search = null;
        $delivery_status = null;
        $payment_status = '';
        $order_type = '';

        $orders = Order::orderBy('id', 'desc');
        $admin_user_id = get_admin()->id;

        if (Route::currentRouteName() == 'inhouse_orders.index' && Auth::user()->can('view_inhouse_orders')) {
            $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
        }
        elseif (Route::currentRouteName() == 'seller_orders.index' && Auth::user()->can('view_seller_orders')) {
            $orders = $orders->where('orders.seller_id', '!=', $admin_user_id);
        }
        elseif (Route::currentRouteName() == 'pick_up_point.index' && Auth::user()->can('view_pickup_point_orders')) {
            if (get_setting('vendor_system_activation') != 1) {
                $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
            }
            $orders->where('shipping_type', 'pickup_point')->orderBy('code', 'desc');
            if (
                Auth::user()->user_type == 'staff' &&
                Auth::user()->staff->pick_up_point != null
            ) {
                $orders->where('shipping_type', 'pickup_point')
                    ->where('pickup_point_id', Auth::user()->staff->pick_up_point->id);
            }
        }
        elseif (Route::currentRouteName() == 'all_orders.index' && Auth::user()->can('view_all_orders')) {
            if (get_setting('vendor_system_activation') != 1) {
                $orders = $orders->where('orders.seller_id', '=', $admin_user_id);
            }
        }
        elseif (Route::currentRouteName() == 'offline_payment_orders.index' && Auth::user()->can('view_all_offline_payment_orders')) {
            $orders = $orders->where('orders.manual_payment', 1);
            if($request->order_type != null){
                $order_type = $request->order_type;
                $orders = $order_type =='inhouse_orders' ? 
                            $orders->where('orders.seller_id', '=', $admin_user_id) : 
                            $orders->where('orders.seller_id', '!=', $admin_user_id);
            }
        }
        elseif (Route::currentRouteName() == 'unpaid_orders.index' && Auth::user()->can('view_all_unpaid_orders')) {
            $orders = $orders->where('orders.payment_status', 'unpaid');
        }
        else {
            abort(403);
        }

        if ($request->search) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }
        if ($request->payment_status != null) {
            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($date != null) {
            $orders = $orders->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])) . '  00:00:00')
                ->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])) . '  23:59:59');
        }
        $orders = $orders->paginate(15);
        $unpaid_order_payment_notification = get_notification_type('complete_unpaid_order_payment', 'type');
        return view('backend.sales.index', compact('orders', 'sort_search', 'order_type', 'payment_status', 'delivery_status', 'date', 'unpaid_order_payment_notification'));
    }

    public function show($id)
    {
        try {
            $decrypted_id = decrypt($id);
        } catch (\Exception $e) {
            abort(404, 'Invalid order ID');
        }
        
        $order = Order::findOrFail($decrypted_id);
        
        // Validate and decode shipping address
        $order_shipping_address = null;
        if (!empty($order->shipping_address)) {
            $order_shipping_address = json_decode($order->shipping_address);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Handle invalid JSON
                $order_shipping_address = null;
            }
        }
        
        $delivery_boys = collect();
        if ($order_shipping_address && isset($order_shipping_address->city)) {
            $delivery_boys = User::where('city', $order_shipping_address->city)
                    ->where('user_type', 'delivery_boy')
                    ->get();
        }
                
        if(env('DEMO_MODE') != 'On') {
            $order->viewed = 1;
            $order->save();
        }

        return view('backend.sales.show', compact('order', 'delivery_boys'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

   // app/Http/Controllers/OrderController.php

public function store(Request $request)
{
    try {
        $userId = null;
        $guestId = null;
        $carts = collect();

        // --- সমাধান: ব্যবহারকারী লগইন করা আছে নাকি অতিথি, তা নির্ধারণ করা ---
        if (Auth::check()) {
            $userId = Auth::user()->id;
            $carts = Cart::where('user_id', $userId)->active()->with('product')->get();
        } else {
            $guestId = $request->session()->get('temp_user_id');
            if (!$guestId) {
                throw new Exception("Your session has expired. Please add items to your cart again.");
            }
            $carts = Cart::where('temp_user_id', $guestId)->active()->with('product')->get();
        }

        if ($carts->isEmpty()) {
            throw new Exception("Your cart is empty.");
        }

        $shippingAddress = $request->session()->get('shipping_info_for_order');
        if (!$shippingAddress) {
            throw new Exception("Shipping information is missing. Please fill in your address.");
        }

        $combined_order = new CombinedOrder;
        $combined_order->user_id = $userId; // লগইন করা থাকলে user_id, না থাকলে null
        // দ্রষ্টব্য: আপনার combined_orders টেবিলে guest_id কলাম না থাকলে এই লাইনটি বাদ দিন।
        // $combined_order->guest_id = $guestId; 
        $combined_order->shipping_address = json_encode($shippingAddress);
        $combined_order->grand_total = 0; // 초기화
        $combined_order->save();

        $seller_products = $carts->groupBy('product.user_id');

        foreach ($seller_products as $seller_id => $seller_cart_items) {
            $order = new Order;
            $order->combined_order_id = $combined_order->id;
            $order->user_id = $userId; // লগইন করা থাকলে user_id, না থাকলে null
            // দ্রষ্টব্য: আপনার orders টেবিলে guest_id কলাম না থাকলে এই লাইনটি বাদ দিন।
            // $order->guest_id = $guestId;
            $order->shipping_address = $combined_order->shipping_address;
            
            $order->shipping_type = $request->delivery_type ?? 'home_delivery';
            if ($order->shipping_type == 'pickup_point') {
                $order->pickup_point_id = $request->pickup_point_id;
            }

            $order->payment_type = $request->payment_option;
            $order->delivery_viewed = '0';
            $order->payment_status_viewed = '0';
            $order->code = date('Ymd-His') . rand(10, 99);
            $order->date = strtotime('now');
            $order->save(); // প্রাথমিক save

            $subtotal = 0;
            $tax = 0;
            $shipping = 0;
            $coupon_discount = 0;

            foreach ($seller_cart_items as $cartItem) {
                $product = $cartItem->product;
                if(!$product){ continue; }

                $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $coupon_discount += $cartItem['discount'];

                $product_variation = $cartItem['variation'];
                $product_stock = $product->stocks->where('variant', $product_variation)->first();

                if ($product->digital != 1 && $product_stock) {
                    if ($cartItem['quantity'] > $product_stock->qty) {
                        throw new Exception(translate('The requested quantity is not available for ') . $product->getTranslation('name'));
                    } else {
                        $product_stock->qty -= $cartItem['quantity'];
                        $product_stock->save();
                    }
                }

                $order_detail = new OrderDetail;
                $order_detail->order_id = $order->id;
                $order_detail->seller_id = $product->user_id;
                $order_detail->product_id = $product->id;
                $order_detail->variation = $product_variation;
                $order_detail->price = cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
                $order_detail->tax = cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
                $order_detail->shipping_type = $order->shipping_type;
                $order_detail->shipping_cost = $cartItem['shipping_cost'] ?? 0;
                $shipping += $order_detail->shipping_cost;
                $order_detail->quantity = $cartItem['quantity'];
                $order_detail->save();

                $product->num_of_sale += $cartItem['quantity'];
                $product->save();

                $order->seller_id = $product->user_id;
            }

            $order->grand_total = $subtotal + $tax + $shipping;

            if ($seller_cart_items->first()->coupon_applied) {
                $order->coupon_discount = $coupon_discount;
                $order->grand_total -= $coupon_discount;
                
                $coupon = Coupon::where('code', $seller_cart_items->first()->coupon_code)->first();
                if($coupon && $userId){
                    $coupon_usage = new CouponUsage;
                    $coupon_usage->user_id = $userId;
                    $coupon_usage->coupon_id = $coupon->id;
                    $coupon_usage->save();
                }
            }

            $order->save();
            $combined_order->grand_total += $order->grand_total;
        }

        $combined_order->save();
        
        $request->session()->put('combined_order_id', $combined_order->id);

    } catch (Exception $e) {
        // এররটিকে CheckoutController-এ ফেরত পাঠানো হচ্ছে, যাতে flash message দেখানো যায়
        throw $e;
    }
}
    //  public function store(Request $request)
    // {
    //     if (!Auth::check()) {
    //         throw new Exception("User not authenticated during order creation.");
    //     }

    //     $user = Auth::user();
    //     $carts = Cart::where('user_id', $user->id)->active()->with('product')->get();

    //     if ($carts->isEmpty()) {
    //         throw new Exception("Your cart is empty.");
    //     }

    //     $shippingAddress = $request->session()->get('shipping_info_for_order');
    //     if (!$shippingAddress) {
    //         throw new Exception("Shipping information not found in session.");
    //     }

    //     $combined_order = new CombinedOrder;
    //     $combined_order->user_id = $user->id;
    //     $combined_order->shipping_address = json_encode($shippingAddress);
    //     $combined_order->save();

    //     $seller_products = $carts->groupBy('product.user_id');

    //     foreach ($seller_products as $seller_id => $seller_cart_items) {
    //         $order = new Order;
    //         $order->combined_order_id = $combined_order->id;
    //         $order->user_id = $user->id;
    //         $order->shipping_address = $combined_order->shipping_address;
            
    //         // **গুরুত্বপূর্ণ:** ফর্ম থেকে delivery_type এবং pickup_point_id সেভ করা হচ্ছে
    //         $order->shipping_type = $request->delivery_type; // 'delivery' or 'pickup'
    //         if ($request->delivery_type == 'pickup') {
    //             $order->pickup_point_id = $request->pickup_point_id;
    //         }

    //         $order->payment_type = $request->payment_option;
    //         $order->delivery_viewed = '0';
    //         $order->payment_status_viewed = '0';
    //         $order->code = date('Ymd-His') . rand(10, 99);
    //         $order->date = strtotime('now');
    //         $order->save();

    //         $subtotal = 0;
    //         $tax = 0;
    //         $shipping = 0;
    //         $coupon_discount = 0;

    //         foreach ($seller_cart_items as $cartItem) {
    //             $product = $cartItem->product;

    //             $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
    //             $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
    //             $coupon_discount += $cartItem['discount'];

    //             $product_variation = $cartItem['variation'];

    //             $product_stock = $product->stocks->where('variant', $product_variation)->first();
    //             if ($product->digital != 1 && $product_stock) {
    //                 if ($cartItem['quantity'] > $product_stock->qty) {
    //                     // **গুরুত্বপূর্ণ পরিবর্তন:** রিডাইরেক্ট না করে Exception থ্রো করা হচ্ছে
    //                     throw new Exception(translate('The requested quantity is not available for ') . $product->getTranslation('name'));
    //                 } else {
    //                     $product_stock->qty -= $cartItem['quantity'];
    //                     $product_stock->save();
    //                 }
    //             }

    //             $order_detail = new OrderDetail;
    //             $order_detail->order_id = $order->id;
    //             $order_detail->seller_id = $product->user_id;
    //             $order_detail->product_id = $product->id;
    //             $order_detail->variation = $product_variation;
    //             $order_detail->price = cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
    //             $order_detail->tax = cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
    //             $order_detail->shipping_type = $request->delivery_type; // Overall delivery type
    //             $order_detail->shipping_cost = $cartItem['shipping_cost'];
    //             $shipping += $order_detail->shipping_cost;
    //             $order_detail->quantity = $cartItem['quantity'];
    //             $order_detail->save();

    //             $product->num_of_sale += $cartItem['quantity'];
    //             $product->save();

    //             $order->seller_id = $product->user_id;

    //             if ($product->added_by == 'seller' && optional($product->user)->seller != null) {
    //                 $seller = $product->user->seller;
    //                 $seller->num_of_sale += $cartItem['quantity'];
    //                 $seller->save();
    //             }
    //         }

    //         $order->grand_total = $subtotal + $tax + $shipping;

    //         if ($seller_cart_items->first()->coupon_code != null) {
    //             $order->coupon_discount = $coupon_discount;
    //             $order->grand_total -= $coupon_discount;

    //             $coupon_usage = new CouponUsage;
    //             $coupon_usage->user_id = $user->id;
    //             $coupon_usage->coupon_id = Coupon::where('code', $seller_cart_items->first()->coupon_code)->first()->id;
    //             $coupon_usage->save();
    //         }

    //         $combined_order->grand_total += $order->grand_total;
    //         $order->save();
    //     }

    //     $combined_order->save();
        
    //     // **সবচেয়ে গুরুত্বপূর্ণ:** সফলভাবে অর্ডার তৈরির পর সেশনে combined_order_id সেট করা
    //     $request->session()->put('combined_order_id', $combined_order->id);
    // }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->single_order_delete($id);
        if ($result) {
            flash(translate('Order has been deleted successfully'))->success();
        } else {
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }

    public function single_order_delete($id)
    {
        $order = Order::findOrFail($id);
        if ($order != null) {
            $order->commissionHistory()->delete();
            foreach ($order->orderDetails as $key => $orderDetail) {
                try {
                    product_restock($orderDetail);
                } catch (\Exception $e) {
                }

                $orderDetail->delete();
            }
            $order->delete();
            return 1;
        } else {
            return 0;
        }
    }

    public function bulk_order_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $order_id) {
                $this->single_order_delete($order_id);
            }
        }

        return 1;
    } 

    public function order_details(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->save();
        return view('seller.order_details_seller', compact('order'));
    }

    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $order->save();

        if($request->status == 'delivered'){
            $order->delivered_date = date("Y-m-d H:i:s");
            $order->save();
        }
        
        if ($request->status == 'cancelled' && $order->payment_type == 'wallet') {
            $user = User::where('id', $order->user_id)->first();
            $user->balance += $order->grand_total;
            $user->save();
        }

        // If the order is cancelled and the seller commission is calculated, deduct seller earning
        if($request->status == 'cancelled' && $order->user->user_type == 'seller' && $order->payment_status == 'paid' && $order->commission_calculated == 1){
            $sellerEarning = $order->commissionHistory->seller_earning;
            $shop = $order->shop;
            $shop->admin_to_pay -= $sellerEarning;
            $shop->save();
        }

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    product_restock($orderDetail);
                }
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {

                $orderDetail->delivery_status = $request->status;
                $orderDetail->save();

                if ($request->status == 'cancelled') {
                    product_restock($orderDetail);
                }

                if (addon_is_activated('affiliate_system')) {
                    if (($request->status == 'delivered' || $request->status == 'cancelled') &&
                        $orderDetail->product_referral_code
                    ) {

                        $no_of_delivered = 0;
                        $no_of_canceled = 0;

                        if ($request->status == 'delivered') {
                            $no_of_delivered = $orderDetail->quantity;
                        }
                        if ($request->status == 'cancelled') {
                            $no_of_canceled = $orderDetail->quantity;
                        }

                        $referred_by_user = User::where('referral_code', $orderDetail->product_referral_code)->first();

                        $affiliateController = new AffiliateController;
                        $affiliateController->processAffiliateStats($referred_by_user->id, 0, 0, $no_of_delivered, $no_of_canceled);
                    }
                }
            }
        }
        // Delivery Status change email notification to Admin, seller, Customer
        EmailUtility::order_email($order, $request->status);  

        // Delivery Status change SMS notification
        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'delivery_status_change')->first()->status == 1) {
            try {
                SmsUtility::delivery_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {}
        }

        //Send web Notifications to user
        NotificationUtility::sendNotification($order, $request->status);

        //Sends Firebase Notifications to user
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->delivery_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('delivery_boy')) {
            if (Auth::user()->user_type == 'delivery_boy') {
                $deliveryBoyController = new DeliveryBoyController;
                $deliveryBoyController->store_delivery_history($order);
            }
        }

        return 1;
    }

    public function update_tracking_code(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->tracking_code = $request->tracking_code;
        $order->save();

        return 1;
    }

    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        if (Auth::user()->user_type == 'seller') {
            foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        } else {
            foreach ($order->orderDetails as $key => $orderDetail) {
                $orderDetail->payment_status = $request->status;
                $orderDetail->save();
            }
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if (
            $order->payment_status == 'paid' &&
            $order->commission_calculated == 0
        ) {
            calculateCommissionAffilationClubPoint($order);
        }

        // Payment Status change email notification to Admin, seller, Customer
        if($request->status == 'paid'){
            EmailUtility::order_email($order, $request->status);  
        }

        //Sends Web Notifications to Admin, seller, Customer
        NotificationUtility::sendNotification($order, $request->status);

        //Sends Firebase Notifications to Admin, seller, Customer
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->payment_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'payment_status_change')->first()->status == 1) {
            try {
                SmsUtility::payment_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {
            }
        }
        return 1;
    }

    public function assign_delivery_boy(Request $request)
    {
        if (addon_is_activated('delivery_boy')) {

            $order = Order::findOrFail($request->order_id);
            $order->assign_delivery_boy = $request->delivery_boy;
            $order->delivery_history_date = date("Y-m-d H:i:s");
            $order->save();

            $delivery_history = \App\Models\DeliveryHistory::where('order_id', $order->id)
                ->where('delivery_status', $order->delivery_status)
                ->first();

            if (empty($delivery_history)) {
                $delivery_history = new \App\Models\DeliveryHistory;

                $delivery_history->order_id = $order->id;
                $delivery_history->delivery_status = $order->delivery_status;
                $delivery_history->payment_type = $order->payment_type;
            }
            $delivery_history->delivery_boy_id = $request->delivery_boy;

            $delivery_history->save();

            if (env('MAIL_USERNAME') != null && get_setting('delivery_boy_mail_notification') == '1') {
                $array['view'] = 'emails.invoice';
                $array['subject'] = translate('You are assigned to delivery an order. Order code') . ' - ' . $order->code;
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['order'] = $order;

                try {
                    Mail::to($order->delivery_boy->email)->queue(new InvoiceEmailManager($array));
                } catch (\Exception $e) {
                }
            }

            if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'assign_delivery_boy')->first()->status == 1) {
                try {
                    SmsUtility::assign_delivery_boy($order->delivery_boy->phone, $order->code);
                } catch (\Exception $e) {
                }
            }
        }

        return 1;
    }

    public function orderBulkExport(Request $request)
    {
        if($request->id){
          return Excel::download(new OrdersExport($request->id), 'orders.xlsx');
        }
        return back();
    }

    public function unpaid_order_payment_notification_send(Request $request){
        if($request->order_ids != null){
            $notificationType = get_notification_type('complete_unpaid_order_payment', 'type');
            foreach (explode(",",$request->order_ids) as $order_id) {
                $order = Order::where('id', $order_id)->first();
                $user = $order->user;
                if($notificationType->status == 1 && $order->payment_status == 'unpaid'){
                    $order_notification['order_id']     = $order->id;
                    $order_notification['order_code']   = $order->code;
                    $order_notification['user_id']      = $order->user_id;
                    $order_notification['seller_id']    = $order->seller_id;
                    $order_notification['status']       = $order->payment_status;
                    $order_notification['notification_type_id'] = $notificationType->id;
                    Notification::send($user, new OrderNotification($order_notification));
                }
            }
            flash(translate('Notification Sent Successfully.'))->success();
        }
        else{
            flash(translate('Something went wrong!.'))->warning();
        }
        return back();
    }

public function completed_orders(Request $request)
{
    // --- Initialize Variables ---
    $sort_search = $request->search;
    $date = $request->date;
    $delivery_status = 'delivered';
    $payment_status = 'paid';
    $order_type = null;

    // --- 1. Get Completed Regular Orders ---
    $regular_orders_query = Order::where('delivery_status', 'delivered')
                                 ->where('payment_status', 'paid')
                                 ->latest();

    if ($sort_search) {
        $regular_orders_query->where('code', 'like', '%' . $sort_search . '%');
    }
    if ($date != null) {
        $regular_orders_query->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])) . ' 00:00:00')
            ->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])) . ' 23:59:59');
    }

    $regular_orders = $regular_orders_query->get();
    $regular_orders->each(function ($order) {
        $order->is_preorder = false;
    });

    // --- 2. Get Completed Pre-Orders ---
    $preorders_query = Preorder::with(['user', 'product.user.shop'])
                                ->where('status', 'completed')
                                ->latest();

    if ($sort_search) {
        $preorders_query->where('order_code', 'like', '%' . $sort_search . '%');
    }
    if ($date != null) {
        $preorders_query->where('created_at', '>=', date('Y-m-d', strtotime(explode(" to ", $date)[0])) . ' 00:00:00')
            ->where('created_at', '<=', date('Y-m-d', strtotime(explode(" to ", $date)[1])) . ' 23:59:59');
    }
    
    $preorder_items = $preorders_query->get();

    // --- 3. Map Pre-Orders to a Standard Order Format ---
    $grouped_preorders = $preorder_items->groupBy('order_code');
    $mapped_preorders = new Collection();

    foreach ($grouped_preorders as $order_code => $items) {
        $representative_item = $items->first();
        $mapped_order = new \stdClass();
        
        // **Map ALL properties that the view file expects**
        $mapped_order->id = $representative_item->id;
        $mapped_order->code = $order_code;
        $mapped_order->user = $representative_item->user;
        $mapped_order->guest_id = $representative_item->guest_id ?? null; // Add guest_id
        $mapped_order->grand_total = $items->sum('grand_total');
        $mapped_order->created_at = $representative_item->created_at;
        $mapped_order->is_preorder = true;
        
        // **FIX for the current error**
        $mapped_order->viewed = 1; // Add the 'viewed' property. 1 means it's not new.
        $mapped_order->order_from = 'website'; // Add 'order_from' to avoid POS errors.
        $mapped_order->refund_requests = []; // Add 'refund_requests' to avoid errors.
        
        $mapped_order->payment_type = $representative_item->payment_method;
        $mapped_order->delivery_status = 'delivered';
        $mapped_order->payment_status = 'paid';
        
        $mapped_order->orderDetails = $items;
        
        if ($representative_item->product_owner_id == get_admin()->id) {
            $mapped_order->shop = null;
        } elseif ($representative_item->product && $representative_item->product->user) {
            $mapped_order->shop = $representative_item->product->user->shop;
        } else {
             $mapped_order->shop = null;
        }

        $mapped_preorders->push($mapped_order);
    }

    // --- 4. Merge, Sort, and Paginate ---
    $all_orders = $regular_orders->toBase()->merge($mapped_preorders);
    $sorted_orders = $all_orders->sortByDesc('created_at');

    $perPage = 15;
    $currentPage = Paginator::resolveCurrentPage('page', 1);
    
    $orders = new LengthAwarePaginator(
        $sorted_orders->forPage($currentPage, $perPage)->values(),
        $sorted_orders->count(),
        $perPage,
        $currentPage,
        ['path' => Paginator::resolveCurrentPath()]
    );

    // --- 5. Return the View with Combined Data ---
    return view('backend.sales.index', compact(
        'orders', 'sort_search', 'order_type', 'payment_status', 'delivery_status', 'date'
    ));
}


}