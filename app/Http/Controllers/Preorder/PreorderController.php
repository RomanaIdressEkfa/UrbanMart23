<?php

namespace App\Http\Controllers\Preorder;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\Carrier;
use App\Models\Cart;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Preorder;
use App\Models\PreorderCoupon;
use App\Models\PreorderProduct;
use App\Utility\PreorderNotificationUtility;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class PreorderController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:preorder_settings'])->only('preorderSettings');
    }

       public function submit_request(Request $request)
    {
        // Find the standard product from the database
        $product = Product::find($request->product_id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.']);
        }

        // Create a new Preorder record to log the request
        $preorder = new Preorder();
        $preorder->user_id = auth()->id();

        // IMPORTANT: We are storing the standard product ID here.
        // Your other logic uses a 'preorder_product_id', so be aware of this difference.
        $preorder->product_id = $product->id;

        $preorder->product_owner_id = $product->user_id;
        $preorder->order_code = date('Ymd-His') . rand(10, 99);

        // We use a custom status to identify this as an out-of-stock request
        $preorder->status = 'out_of_stock_request';
        $preorder->request_preorder_status = 1; // Mark as a request
        $preorder->request_preorder_time = now();

        // Set default values for other required fields
        $preorder->grand_total = 0;
        $preorder->subtotal = 0;
        $preorder->quantity = 1; // Default to 1 for a simple request

        $preorder->save();

        // You can uncomment the line below if you want to send a notification
        // PreorderNotificationUtility::preorderNotification($preorder, 'request');

        return response()->json([
            'success' => true,
            'message' => 'Thank you! We have received your pre-order request and will contact you soon.'
        ]);
    }


    public function place_order(Request $request){

        $product = PreorderProduct::find($request->preorder_product_id);
        $tax = 0;
        $product_discount = 0;
        foreach ($product->taxes as $product_tax) {
            if ($product_tax->tax_type == 'percent') {
                $tax += (($product->unit_price * $product->min_qty) * $product_tax->tax) / 100;
            } elseif ($product_tax->tax_type == 'amount') {
                $tax += $product_tax->tax;
            }
        }

        $tax *= $product->min_qty;
        $shipping_cost = 0;
        if($product->preorder_shipping->shipping_type != null && $product->preorder_shipping->shipping_type = 'flat'){
            $shipping_cost += get_setting('preorder_flat_rate_shipping');
        }

        $product_discount = $product->discount_type == 'flat' ?   $product->discount : ($product->discount * $product->unit_price) /100;

        $currentTimestamp = strtotime(date('d-m-Y'));

        if($product->discount_start_date == null && ($currentTimestamp < $product->discount_start_date || $currentTimestamp > $product->discount_end_date)){
            $product_discount = 0;
        }

        $product_price = $product->unit_price - $product_discount;

        $total_product_price = $product_price * $product->min_qty;

        $preorder = new Preorder();

        $preorder->product_id  = $request->preorder_product_id;
        $preorder->user_id  = auth()->id();
        $preorder->product_owner_id  = $product->user_id;
        $preorder->product_owner = $product->user->user_type;
        $preorder->subtotal  = $product_price * $product->min_qty;
        $preorder->grand_total  = $total_product_price + $tax + $shipping_cost;
        $preorder->tax  = $tax;
        $preorder->product_discount  = $product_discount * $product->min_qty;
        $preorder->shipping_cost  = $shipping_cost;
        $preorder->quantity  = $product->min_qty;
        $preorder->unit_price  = $product->unit_price;
        $preorder->order_code  = date('Ymd-His') . rand(10, 99);
        $preorder->request_note = $request->request_note;
        $preorder->request_preorder_status = 1;
        $preorder->request_preorder_time = now();
        $preorder->save();
        $statusType = 'request';

        // Store preorder ID and total in session for payment processing
        $request->session()->put('preorder_ids', [$preorder->id]);
        $request->session()->put('preorder_total', $preorder->grand_total);

        flash(translate('Preorder request submitted successfully!!'))->success();
        return redirect()->route('preorder.order_details',encrypt($preorder->id));
    }

    public function order_list(){
        $orders = Preorder::where('user_id', auth()->id())->orderBy('created_at','desc')->paginate(15);
        return view('preorder.frontend.order.purchase_history', compact('orders'));
    }

    public function order_details (Request $request, $id){
        $order = Preorder::with('preorder_product')->find(decrypt($id));

        if(!$order){
            flash(translate('No order found!!'))->success();
            return redirect()->back();
        }

        $sort_search = '';

        if (get_setting('guest_checkout_activation') == 0 && auth()->user() == null) {
            return redirect()->route('user.login');
        }

        if (auth()->check() && !$request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        $country_id = 0;
        $city_id = 0;
        $address_id = 0;
        $shipping_info = array();
        $shipping_info['country_id'] = $country_id;
        $shipping_info['city_id'] = $city_id;

        $default_carrier_id = null;
        $default_shipping_type = 'home_delivery';

        $carrier_list = Carrier::where('status', 1)->get();
        $review_status =  (auth()->check() && (Preorder::whereProductId($order->preorder_product->id)->where('user_id', auth()->user()->id)->whereDeliveryStatus(2)->count() > 0) ) ? 1 : 0;

        return view('preorder.frontend.order.show', compact('sort_search', 'order',  'address_id',  'carrier_list', 'shipping_info','review_status'));
    }

    public function order_update(Request $request, $id){
        $order = Preorder::find($id);
        // dd($request->all());
        if(! $order){
            flash(translate('No order found!!'))->success();
            return redirect()->back();
        }

        if($request->request_preorder){
            $order->request_note = $request->request_note;
            $order->request_preorder_status = 1;
            $order->request_preorder_time = now();
            $order->status = 'request_preorder_status';
            $order->save();
            $statusType = 'request';
        }

        if($request->prepayment_confirmation){
            $order->prepayment  = $order->preorder_product?->preorder_prepayment?->prepayment_amount;
            $order->payment_proof = $request->payment_proof;
            $order->reference_no = $request->reference_no;
            $order->confirm_note = $request->confirm_note;
            $order->cod_for_prepayment = $request->cod_for_prepayment;
            $order->prepayment_confirm_status = 1;
            $order->prepayment_confirmation_time = now();
            $statusType = 'prepayment_request';
            $order->status = 'prepayment_confirm_status';
            $order->save();
        }

        if($request->final_order){
            $order->address_id = $request->address_id;
            $order->pickup_point_id = $request->pickup_point_id;
            $order->delivery_type = $request->shipping_type;
            $order->cod_for_final_order = $request->cod_for_final_order;
            $order->prepayment_confirmation_time = now();
            $order->final_order_status = 1;
            $order->final_payment_proof = $request->final_payment_proof;
            $order->final_payment_reference_no = $request->final_payment_reference_no;
            $order->final_payment_confirm_note = $request->final_payment_confirm_note;
            $statusType = 'final_request';
            $order->status = 'final_order_status';
            $order->save();
        }

        if($request->refund_request){
            $order->refund_status = 1;
            $order->refund_proof = $request->refund_proof;
            $order->refund_note = $request->refund_note;
            $statusType = 'product_refund_request';
            $order->status = 'refund_status';
            $order->save();
        }

        //Send web Notifications to user, product Owner, if product Owner is not admin, admin too
        PreorderNotificationUtility::preorderNotification($order, $statusType);

        flash(translate('Order updated!!'))->success();
        return redirect()->back();
    }

    // PreOrder Settings
    public function preorderSettings(){
        return view('preorder.backend.settings.index');
    }


public function updateDeliveryAddress(Request $request)
{
    if ($request->session()->has('preorder_ids')) {

        $preorder_ids = $request->session()->get('preorder_ids');
        $preorders = Preorder::whereIn('id', $preorder_ids)->get();

        $total_prepayment = $preorders->sum('prepayment');
        $request->session()->put('preorder_total', $total_prepayment);

        // ধাপ ৪: শুধুমাত্র preorder_summary ভিউ রেন্ডার করে JSON হিসেবে পাঠানো
        // এই কোডটি কোনোভাবেই delivery_info.blade.php ফাইলকে কল করবে না
        return response()->json([
            'preorder_summary' => view('preorder.frontend.partials.preorder_summary', [
                'preorders_for_summary' => $preorders,
                'advance_amount' => $total_prepayment
            ])->render()
        ]);
    }

    // যদি কোনো কারণে এই মেথডটি প্রি-অর্ডার ফ্লো ছাড়া অন্য কোথাও থেকে কল হয়,
    // তাহলে একটি খালি রেসপন্স পাঠানো হবে যেন কোনো Error না হয়।
    return response()->json([]);
}



//    public function updateDeliveryAddress(Request $request)
// {
//     $user = auth()->user();
//     $carts = $user ? Cart::where('user_id', $user->id)->active()->get() : Cart::where('temp_user_id', $request->session()->get('temp_user_id'))->active()->get();

//     if ($carts->isEmpty() && $request->session()->has('preorder_ids')) {

//         $preorder_ids = $request->session()->get('preorder_ids');
//         $preorders = Preorder::whereIn('id', $preorder_ids)->get();

//         $shipping_info = [
//             'country_id' => 18,
//             'city_id' => $request->city_id,
//         ];

//         foreach ($preorders as $preorder) {
//             $temp_cart_item = new \stdClass();
//             $temp_cart_item->product_id = $preorder->product_id;
//             $temp_cart_item->quantity = $preorder->quantity;
//             $temp_cart_item->user_id = $preorder->user_id;
//             $temp_carts_collection = collect([$temp_cart_item]);

//             $shipping_cost = getShippingCost($temp_carts_collection, 0, $shipping_info);

//             $preorder->shipping_cost = $shipping_cost;
//             $preorder->grand_total = $preorder->subtotal + $preorder->tax + $shipping_cost;

//             $product = Product::find($preorder->product_id);
//             if($product){
//                 $percentage = $product->getPreorderPaymentPercentage();
//                 $preorder->prepayment = $preorder->grand_total * ($percentage / 100);
//             }
//             $preorder->save();
//         }

//         $total_prepayment = $preorders->sum('prepayment');
//         $request->session()->put('preorder_total', $total_prepayment);
//         return response()->json([
//             'preorder_summary' => view('preorder.frontend.partials.preorder_summary', [
//                 'preorders_for_summary' => $preorders,
//                 'advance_amount' => $total_prepayment
//             ])->render()
//         ]);
//     }

//     $proceed = 0;
//     $default_carrier_id = null;
//     $default_shipping_type = 'home_delivery';

//     $carts->toQuery()->update(['address_id' => $request->address_id]);

//     $country_id = $user ? Address::findOrFail($request->address_id)->country_id : $request->address_id;
//     $city_id = $user ? Address::findOrFail($request->address_id)->city_id : $request->city_id;

//     $shipping_info = [
//         'country_id' => $country_id,
//         'city_id' => $city_id,
//     ];

//     $carrier_list = array();
//     if (get_setting('shipping_type') == 'carrier_wise_shipping') {
//         $default_shipping_type = 'carrier';
//         $zone = Country::where('id', $country_id)->first()->zone_id;

//         $carrier_query = Carrier::where('status', 1);
//         $carrier_query->whereIn('id',function ($query) use ($zone) {
//             $query->select('carrier_id')->from('carrier_range_prices')
//                 ->where('zone_id', $zone);
//         })->orWhere('free_shipping', 1);
//         $carrier_list = $carrier_query->get();

//         if (count($carrier_list) > 1) {
//             $default_carrier_id = $carrier_list->toQuery()->first()->id;
//         }
//     }

//     $carts = $carts->fresh();

//     foreach ($carts as $key => $cartItem) {
//         if (get_setting('shipping_type') == 'carrier_wise_shipping') {
//             $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info, $default_carrier_id);
//         } else {
//             $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info);
//         }
//         $cartItem['address_id'] = $user ? $request->address_id : 0;
//         $cartItem['shipping_type'] = $default_shipping_type;
//         $cartItem['carrier_id'] = $default_carrier_id;
//         $cartItem->save();
//     }

//     $carts = $carts->fresh();

//     return response()->json([
//         'delivery_info' => view('frontend.partials.cart.delivery_info', compact('carts', 'carrier_list', 'shipping_info'))->render(),
//         'cart_summary' => view('frontend.partials.cart.cart_summary', compact('carts', 'proceed'))->render()
//     ]);
// }

    public function apply_coupon_code(Request $request){
        $coupon = PreorderCoupon::where('preorder_product_id', $request->preorder_product_id)->where('coupon_code',$request->coupon_code)->first();


        // Coupon Code Invalid
        if(!$coupon){
            flash(translate('Coupon is invalid!!'))->error();
            return redirect()->back();
        }

        if(!$coupon->preorder_product?->is_coupon){
            flash(translate('Coupon is not enabled for this product'))->warning();
            return redirect()->back();
        }

        $currentTimestamp = strtotime(date('d-m-Y'));

        if ($currentTimestamp < $coupon->coupon_start_date || $currentTimestamp > $coupon->coupon_end_date) {
            flash(translate('Coupon is invalid!!'))->error();
            return redirect()->back();
        }

        $order = Preorder::whereId($request->order_id)->first();
        $discount = $coupon->coupon_type == 'percent' ? ($order->subtotal * $coupon->coupon_amount)/100 : $coupon->coupon_amount;
        $order->is_coupon_applied = 1;
        $order->coupon_discount = $discount;
        $order->grand_total  -= $discount;
        $order->save();

        flash(translate('Coupon Applied!!'))->success();
        return redirect()->back();
    }

    public function remove_coupon_code(Request $request){
        $order = Preorder::whereId($request->order_id)->first();
        $order->is_coupon_applied = 0;
        $order->grand_total  += $order->coupon_discount;
        $order->coupon_discount = null;
        $order->save();

        flash(translate('Coupon Removed Successfully!!'))->success();
        return redirect()->back();
    }

    // Mohammad Hassan
    public function create_preorder_from_cart(Request $request)
    {
        $user = auth()->user();
        $temp_user_id = $request->session()->get('temp_user_id');

        if ($user) {
            $carts = Cart::where('user_id', $user->id)->get();
        } else {
            $carts = $temp_user_id ? Cart::where('temp_user_id', $temp_user_id)->get() : collect();
        }

        if ($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->error();
            return redirect()->route('home');
        }

        // Check if all products are out of stock
        $out_of_stock_products = [];
        foreach ($carts as $cart) {
            $product = Product::find($cart->product_id);
            if ($product && $product->isOutOfStock()) {
                $out_of_stock_products[] = $cart;
            }
        }

        if (empty($out_of_stock_products)) {
            flash(translate('No out of stock products found in cart'))->error();
            return redirect()->route('checkout.shipping_info');
        }

        $total_amount = 0;
        $preorders = [];

        foreach ($out_of_stock_products as $cart) {
            $product = Product::find($cart->product_id);
            $preorder_price = $product->getPreorderPrice(); // 50% of unit price
            $quantity = $cart->quantity;
            $subtotal = $preorder_price * $quantity;

            // Include tax and shipping from cart
            $tax = $cart->tax ?? 0;
            $shipping_cost = $cart->shipping_cost ?? 0;
            $grand_total = $subtotal + $tax + $shipping_cost;
            // Use product-level preorder payment percentage (defaults to 50%)
            $percentage = $product->getPreorderPaymentPercentage();
            $prepayment = $grand_total * ($percentage / 100);

            $preorder = new Preorder();
            $preorder->user_id = $user ? $user->id : null;
            $preorder->product_id = $product->id;
            $preorder->product_owner_id = $product->user_id;
            $preorder->product_owner = $product->user->user_type ?? 'seller';
            $preorder->quantity = $quantity;
            $preorder->unit_price = $product->unit_price;
            $preorder->subtotal = $subtotal;
            $preorder->tax = $tax;
            $preorder->shipping_cost = $shipping_cost;
            $preorder->grand_total = $grand_total;
            $preorder->prepayment = $prepayment;
            $preorder->order_code = date('Ymd-His') . rand(10, 99);
            $preorder->status = 'pending_payment';
            $preorder->prepayment_confirm_status = 0;
            $preorder->final_order_status = 0;
            $preorder->created_at = now();
            $preorder->updated_at = now();

            // Store guest shipping info if applicable
            if (!$user && $request->session()->has('guest_shipping_info')) {
                $shipping_info = $request->session()->get('guest_shipping_info');
                $preorder->guest_shipping_info = json_encode($shipping_info);
            } elseif ($user && $cart->address_id) {
                $preorder->address_id = $cart->address_id;
            }

            $preorder->save();
            $preorders[] = $preorder;
            $total_amount += $prepayment; // Use prepayment amount for total

            // Remove from cart
            $cart->delete();
        }

        // Store preorder IDs in session for payment processing
        $request->session()->put('preorder_ids', collect($preorders)->pluck('id')->toArray());
        $request->session()->put('preorder_total', $total_amount);

        return redirect()->route('preorder.payment_selection');
    }

    // Mohammad Hassan
    public function shipping_info()
    {
        $preorder_ids = session('preorder_ids');

        if (!$preorder_ids) {
            flash(translate('No preorder found'))->error();
            return redirect()->route('home');
        }

        $addresses = collect();
        $address_id = 0;

        if (auth()->check()) {
            $user = Auth::user();
            $user_id = $user->id;
            $addresses = Address::where('user_id', $user_id)->with('city')->get();

            // Get user info for auto-fill
            $user_info = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone
            ];

            if(count($addresses)){
                $address = $addresses->first();
                $address_id = $address->id;
                $default_address = $addresses->where('set_default', 1)->first();
                if($default_address != null){
                    $address_id = $default_address->id;
                }
            }

            // Pass user info to view
            view()->share('user_info', $user_info);
        }

        return view('preorder.frontend.shipping_info', compact('addresses', 'address_id'));
    }

    // Mohammad Hassan
    public function payment_selection()
    {
        $preorder_ids = session('preorder_ids');
        $total_amount = session('preorder_total');

        if (!$preorder_ids || !$total_amount) {
            flash(translate('No preorder found'))->error();
            return redirect()->route('home');
        }

        $preorders = Preorder::whereIn('id', $preorder_ids)->get();

        // Add address_id logic similar to checkout controller
        $address_id = 0;
        $addresses = collect();

        if (auth()->check()) {
            $user_id = Auth::user()->id;
            $addresses = Address::where('user_id', $user_id)->with('city')->get();
            if(count($addresses)){
                $address = $addresses->first();
                $address_id = $address->id;
                $default_address = $addresses->where('set_default', 1)->first();
                if($default_address != null){
                    $address_id = $default_address->id;
                }
            }
        }

        return view('preorder.frontend.payment_selection', compact('preorders', 'total_amount', 'address_id', 'addresses'));
    }



    public function process_payment(Request $request)
{
    // ধাপ ১: ভ্যালিডেশন চেক করা (Error মেসেজ সহ)
    if (empty($request->name) || empty($request->email) || empty($request->phone) || empty($request->city_id)) {
        flash(translate('Please fill out all required shipping information fields.'))->error();
        return back();
    }
    if ($request->delivery_type == 'delivery' && empty($request->address)) {
        flash(translate('Please provide a delivery address.'))->error();
        return back();
    }
    if ($request->delivery_type == 'pickup' && empty($request->pickup_point_id)) {
        flash(translate('Please select a pickup point.'))->error();
        return back();
    }
    if (empty($request->payment_option)) {
        flash(translate('Please select a payment method.'))->error();
        return back();
    }

    // --- বাকি কোড ---

    $preorder_ids = session('preorder_ids');
    $payment_option = $request->payment_option;

    if (!$preorder_ids) {
        flash(translate('No preorder found. Please try again.'))->error();
        return redirect()->route('home');
    }

    $shipping_city_name = null;
    if($request->city_id) {
        $city = \App\Models\City::find($request->city_id);
        if($city) {
            $shipping_city_name = $city->name;
        }
    }

    // preorders টেবিলে শিপিং তথ্য এবং পেমেন্ট মেথড একসাথে আপডেট করা
    Preorder::whereIn('id', $preorder_ids)->update([
        'payment_method'        => $payment_option,
        'status'                => 'payment_processing',
        'shipping_name'         => $request->name,
        'shipping_email'        => $request->email,
        'shipping_phone'        => $request->phone,
        'shipping_city'         => $shipping_city_name,
        'shipping_address'      => $request->address,
        'shipping_pickup_point' => $request->pickup_point_id,
        'delivery_type'         => $request->delivery_type
    ]);

    // ক্যাশ অন ডেলিভারি হ্যান্ডেল করা
    if ($payment_option === 'cash_on_delivery') {
        return $this->process_cod_payment();
    }

    // পেমেন্ট গেটওয়েতে রিডাইরেক্ট করা
    switch ($payment_option) {
        case 'stripe':
            // আপনার Stripe ইন্টিগ্রেশন অনুযায়ী রাউট ঠিক করুন
            // return redirect()->route('stripe.payment', ['type' => 'preorder']);
            flash(translate('Stripe is not configured yet.'))->error();
            return back();
        case 'paypal':
            // আপনার PayPal ইন্টিগ্রেশন অনুযায়ী রাউট ঠিক করুন
            // return redirect()->route('paypal.payment', ['type' => 'preorder']);
            flash(translate('PayPal is not configured yet.'))->error();
            return back();
        case 'razorpay':
            // আপনার Razorpay ইন্টিগ্রেশন অনুযায়ী রাউট ঠিক করুন
            // return redirect()->route('razorpay.payment', ['type' => 'preorder']);
            flash(translate('Razorpay is not configured yet.'))->error();
            return back();
        case 'sslcommerz':
            session(['payment_type' => 'preorder_payment']);
            session(['payment_data' => [
                'preorder_ids' => $preorder_ids,
                'amount' => session('preorder_total')
            ]]);
            return redirect()->route('sslcommerz.pay');
        case 'paystack':
            // আপনার Paystack ইন্টিগ্রেশন অনুযায়ী রাউট ঠিক করুন
            // return redirect()->route('paystack.payment', ['type' => 'preorder']);
            flash(translate('Paystack is not configured yet.'))->error();
            return back();
        case 'bkash':
            session(['payment_type' => 'preorder_payment']);
            session(['payment_data' => [
                'preorder_ids' => $preorder_ids,
                'amount' => session('preorder_total')
            ]]);
            // আপনার bKash ইন্টিগ্রেশন অনুযায়ী রাউট ঠিক করুন
            return redirect()->route('bkash.payment', ['type' => 'preorder']);
        case 'nagad':
            // আপনার Nagad ইন্টিগ্রেশন অনুযায়ী রাউট ঠিক করুন
            // return redirect()->route('nagad.payment', ['type' => 'preorder']);
            flash(translate('Nagad is not configured yet.'))->error();
            return back();
        case 'wallet':
            return $this->process_wallet_payment();
        default:
            flash(translate('Invalid payment method selected.'))->error();
            return redirect()->back();
    }
}

    // Mohammad Hassan
    private function process_cod_payment()
    {
        $preorder_ids = session('preorder_ids');

        // Update preorders as confirmed with COD
        Preorder::whereIn('id', $preorder_ids)->update([
            'prepayment_confirm_status' => 1,
            'prepayment_confirm_time' => now(),
            'status' => 'confirmed',
            'payment_details' => json_encode(['method' => 'cash_on_delivery'])
        ]);

        // Send notifications
        $preorders = Preorder::whereIn('id', $preorder_ids)->get();
        foreach ($preorders as $preorder) {
            // Send notification to seller about new preorder
            if (class_exists('App\Utility\PreorderNotificationUtility')) {
                \App\Utility\PreorderNotificationUtility::preorderNotification($preorder, 'prepayment_confirmed');
            }
        }

        // Clear session
        session()->forget(['preorder_ids', 'preorder_total']);

        flash(translate('Preorder confirmed successfully! You will be notified when products are available.'))->success();
        return redirect()->route('preorder.payment_success');
    }

    // Mohammad Hassan
    public function payment_success()
    {
        return view('preorder.frontend.payment_success');
    }

    // Mohammad Hassan
    public function payment_cancel()
    {
        $preorder_ids = session('preorder_ids');

        if ($preorder_ids) {
            // Reset preorder status
            Preorder::whereIn('id', $preorder_ids)->update([
                'status' => 'pending_payment'
            ]);
        }

        flash(translate('Payment was cancelled. You can try again.'))->warning();
        return redirect()->route('preorder.payment_selection');
    }

    // Mohammad Hassan
    private function process_wallet_payment()
    {
        $user = auth()->user();
        $preorder_ids = session('preorder_ids');
        $total_amount = session('preorder_total');

        if (!$user) {
            flash(translate('Please login to use wallet payment'))->error();
            return redirect()->route('user.login');
        }

        if ($user->balance < $total_amount) {
            flash(translate('Insufficient wallet balance'))->error();
            return redirect()->back();
        }

        // Deduct from wallet
        $user->balance -= $total_amount;
        $user->save();

        // Update preorders
        Preorder::whereIn('id', $preorder_ids)->update([
            'prepayment_confirm_status' => 1,
            'prepayment_confirmation_time' => now(),
            'status' => 'confirmed',
            'payment_details' => json_encode(['method' => 'wallet', 'amount' => $total_amount])
        ]);

        // Clear session
        session()->forget(['preorder_ids', 'preorder_total']);

        flash(translate('Preorder payment successful! You will be notified when products are available.'))->success();
        return redirect()->route('preorder.order_list');
    }

    // Mohammad Hassan
    public function preorder_payment_success($payment_details = null)
    {
        $preorder_ids = session('preorder_ids');

        if (!$preorder_ids) {
            flash(translate('No preorder found'))->error();
            return redirect()->route('home');
        }

        // Update preorders as paid
        Preorder::whereIn('id', $preorder_ids)->update([
            'prepayment_confirm_status' => 1,
            'prepayment_confirmation_time' => now(),
            'status' => 'confirmed',
            'payment_details' => $payment_details
        ]);

        // Send notifications
        $preorders = Preorder::whereIn('id', $preorder_ids)->get();
        foreach ($preorders as $preorder) {
            PreorderNotificationUtility::preorderNotification($preorder, 'prepayment_confirmed');
        }

        // Clear session
        session()->forget(['preorder_ids', 'preorder_total']);

        flash(translate('Preorder payment successful! You will be notified when products are available.'))->success();
        // Redirect to a guest-accessible success page
        return redirect()->route('preorder.payment_success');
    }

    // Mohammad Hassan
    public function notify_product_arrival(Request $request)
    {
        $preorder_ids = $request->preorder_ids;

        if (empty($preorder_ids)) {
            flash(translate('No preorders selected'))->error();
            return back();
        }

        foreach ($preorder_ids as $preorder_id) {
            $preorder = Preorder::find($preorder_id);
            if ($preorder && $preorder->prepayment_confirm_status == 'confirmed') {
                $preorder->status = 'product_available';
                $preorder->save();

                // Send comprehensive notification to customer
                \App\Utility\PreorderArrivalNotificationUtility::sendProductArrivalNotification($preorder);
            }
        }

        flash(translate('Product arrival notifications sent successfully'))->success();
        return back();
    }

    // Mohammad Hassan
    public function complete_preorder_payment(Request $request, $preorder_id)
    {
        $preorder = Preorder::findOrFail($preorder_id);

        if ($preorder->user_id !== auth()->id()) {
            flash(translate('Unauthorized access'))->error();
            return redirect()->back();
        }

        if ($preorder->status !== 'product_available') {
            flash(translate('Product is not yet available'))->error();
            return redirect()->back();
        }

        $remaining_amount = $preorder->getRemainingPrice() * $preorder->quantity;

        return view('preorder.frontend.complete_payment', compact('preorder', 'remaining_amount'));
    }

    // Mohammad Hassan
    public function process_final_payment(Request $request, $preorder_id)
    {
        $preorder = Preorder::findOrFail($preorder_id);
        $payment_method = $request->payment_method;
        $remaining_amount = $preorder->getRemainingPrice() * $preorder->quantity;

        // Store in session for payment processing
        session(['final_payment_preorder_id' => $preorder_id, 'final_payment_amount' => $remaining_amount]);

        // Redirect to payment gateway
        switch ($payment_method) {
            case 'stripe':
                return redirect()->route('stripe.payment', ['type' => 'preorder_final']);
            case 'paypal':
                return redirect()->route('paypal.payment', ['type' => 'preorder_final']);
            case 'wallet':
                return $this->process_final_wallet_payment($preorder_id, $remaining_amount);
            default:
                flash(translate('Invalid payment method'))->error();
                return redirect()->back();
        }
    }

    // Mohammad Hassan
    private function process_final_wallet_payment($preorder_id, $amount)
    {
        $user = auth()->user();
        $preorder = Preorder::findOrFail($preorder_id);

        if ($user->balance < $amount) {
            flash(translate('Insufficient wallet balance'))->error();
            return redirect()->back();
        }

        // Deduct from wallet
        $user->balance -= $amount;
        $user->save();

        // Update preorder
        $preorder->final_order_status = 1;
        $preorder->final_payment_confirmation_time = now();
        $preorder->status = 'completed';
        $preorder->final_payment_details = json_encode(['method' => 'wallet', 'amount' => $amount]);
        $preorder->save();

        // Create regular order from preorder
        $this->create_order_from_preorder($preorder);

        flash(translate('Final payment successful! Your order has been placed.'))->success();
        return redirect()->route('preorder.order_list');
    }

    // Mohammad Hassan
    public function final_payment_success($preorder_id, $payment_details = null)
    {
        $preorder = Preorder::findOrFail($preorder_id);

        // Update preorder
        $preorder->final_order_status = 1;
        $preorder->final_payment_confirmation_time = now();
        $preorder->status = 'completed';
        $preorder->final_payment_details = $payment_details;
        $preorder->save();

        // Create regular order from preorder
        $this->create_order_from_preorder($preorder);

        // Clear session
        session()->forget(['final_payment_preorder_id', 'final_payment_amount']);

        flash(translate('Final payment successful! Your order has been placed.'))->success();
        return redirect()->route('preorder.order_list');
    }

    // Mohammad Hassan
    private function create_order_from_preorder($preorder)
    {
        // Create a regular order from the completed preorder
        $order = new Order();
        $order->user_id = $preorder->user_id;
        $order->guest_id = $preorder->guest_id ?? null;
        $order->seller_id = $preorder->product_owner_id;
        $order->code = 'ORD-' . date('Ymd-His') . rand(10, 99);
        $order->date = now();
        $order->payment_type = 'preorder_completed';
        $order->payment_status = 'paid';
        $order->delivery_status = 'pending';
        $order->grand_total = $preorder->unit_price * $preorder->quantity;
        $order->coupon_discount = 0;
        $order->shipping_cost = 0;
        $order->is_preorder = true;
        $order->preorder_status = 'completed';
        $order->paid_amount = $preorder->unit_price * $preorder->quantity;
        $order->preorder_notes = 'Order created from completed preorder #' . $preorder->order_code;
        $order->completed_at = now();
        $order->save();

        // Create order detail
        $order_detail = new OrderDetail();
        $order_detail->order_id = $order->id;
        $order_detail->product_id = $preorder->product_id;
        $order_detail->variation = '';
        $order_detail->price = $preorder->unit_price;
        $order_detail->tax = 0;
        $order_detail->shipping_cost = 0;
        $order_detail->quantity = $preorder->quantity;
        $order_detail->payment_status = 'paid';
        $order_detail->delivery_status = 'pending';
        $order_detail->shipping_type = null;
        $order_detail->product_referral_code = null;
        $order_detail->save();

        // Update preorder to mark as converted
        $preorder->converted_to_order = 1;
        $preorder->order_id = $order->id;
        $preorder->save();

        return $order;
    }


public function direct_checkout(Request $request)
{
    if (!$request->has('product_id') || !$request->has('selected_items')) {
        flash(translate('Invalid pre-order request. Please select items first.'))->error();
        return back();
    }

    $product = \App\Models\Product::find($request->product_id);
    if (!$product || !$product->isPreorderAvailable()) {
        flash(translate('This product is not available for pre-order.'))->error();
        return back();
    }

    $selectedItems = json_decode($request->selected_items, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($selectedItems) || empty($selectedItems)) {
        flash(translate('Please select at least one item to pre-order.'))->error();
        return back();
    }

    $preorderIds = [];
    $totalPrepayment = 0;
    $common_order_code = date('Ymd-His') . rand(10, 99);

    foreach ($selectedItems as $item) {
        $quantity = (int)($item['quantity'] ?? 0);
        if ($quantity <= 0) continue;

        $stock = \App\Models\ProductStock::find($item['stock_id']);
        if (!$stock) continue;

        // *** প্রধান পরিবর্তন: ফ্রন্টএন্ড থেকে পাঠানো unit_price সরাসরি ব্যবহার করা হচ্ছে ***
        // যেহেতু আমরা আগের ধাপে জাভাস্ক্রিপ্টে সঠিক দাম পাঠানোর ব্যবস্থা করেছি।
        $unit_price = (float)($item['unit_price'] ?? $stock->price);
        
        $subtotal = $unit_price * $quantity;
        $grand_total = $subtotal; // tax এবং shipping আপাতত ০

        $preorder = new Preorder();
        $preorder->product_id = $product->id;
        $preorder->user_id = auth()->id();
        $preorder->subtotal = $subtotal;
        $preorder->grand_total = $grand_total;
        $preorder->quantity = $quantity;
        $preorder->unit_price = $unit_price; // <<<--- সঠিক দাম এখানে সেভ হচ্ছে
        $preorder->variant_name = $item['variant_name'] ?? $stock->variant;
        $preorder->stock_id = $item['stock_id'];
        $preorder->order_code = $common_order_code;
        $preorder->status = 'pending_payment';

        $percentage = $product->getPreorderPaymentPercentage();
        $preorder->prepayment = $grand_total * ($percentage / 100);
        $preorder->save();

        $preorderIds[] = $preorder->id;
        $totalPrepayment += $preorder->prepayment;
    }

    if (empty($preorderIds)) {
        flash(translate('No valid items were selected for pre-order.'))->error();
        return back();
    }

    $request->session()->put('preorder_ids', $preorderIds);
    $request->session()->put('preorder_total', $totalPrepayment);

    return redirect()->route('preorder.payment_selection');
}


   public function save_shipping_info(Request $request)
{
    // ১. ফর্ম থেকে আসা ডেটা ভ্যালিডেট করা
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|max:20',
        'city_id' => 'required|integer',
        'address' => 'required_if:delivery_type,delivery|nullable|string|max:500',
        'pickup_point_id' => 'required_if:delivery_type,pickup|nullable|integer',
        'delivery_type' => 'required|in:delivery,pickup'
    ]);

    // ২. সেশন থেকে প্রি-অর্ডার আইডিগুলো নেওয়া
    if ($request->session()->has('preorder_ids')) {
        $preorder_ids = $request->session()->get('preorder_ids');

        $shipping_city_name = null;
        if($request->city_id) {
            $city = \App\Models\City::find($request->city_id);
            if($city) {
                $shipping_city_name = $city->name;
            }
        }

        // ৩. Preorder টেবিলে শিপিং তথ্য আপডেট করা
        Preorder::whereIn('id', $preorder_ids)->update([
            'shipping_name'         => $request->name,
            'shipping_email'        => $request->email,
            'shipping_phone'        => $request->phone,
            'shipping_city'         => $shipping_city_name,
            'shipping_address'      => $request->address,
            'shipping_pickup_point' => $request->pickup_point_id, // pickup_point_id এখন এখানে সেভ হবে
            'delivery_type'         => $request->delivery_type
        ]);

        // ৪. সেশন আপডেট করা
        $request->session()->put('preorder_shipping_saved', true);

        flash(translate('Shipping information saved successfully!'))->success();
        return redirect()->route('preorder.payment_selection');
    }

    flash(translate('Could not find your preorder. Please try again.'))->error();
    return redirect()->route('home');
}

    /**
     * Show the specified pre-order.
     * Display preorder details for admin view
     */
    public function show($id)
    {
        $order = Preorder::with(['preorder_product', 'user', 'address'])
                           ->findOrFail($id);

        return view('preorder.backend.orders.show', compact('order'));
    }

    /**
     * Display preorder index page for admin
     */
    public function index(Request $request)
    {
        $sort_search = null;

        $preorders = Preorder::with(['preorder_product', 'user'])
                            ->orderBy('created_at', 'desc');

        if ($request->has('search')) {
            $sort_search = $request->search;
            $preorders = $preorders->where('order_code', 'like', '%'.$sort_search.'%');
        }

        $preorders = $preorders->paginate(15);

        return view('backend.preorders.index', compact('preorders', 'sort_search'));
    }


    public function process_cod_payment_from_checkout()
{
    $preorder_ids = session('preorder_ids');

    if(!$preorder_ids) {
        return redirect()->route('home');
    }

    // Update preorders as confirmed with COD
    Preorder::whereIn('id', $preorder_ids)->update([
        'prepayment_confirm_status' => 1,
        'status' => 'confirmed',
        'payment_details' => json_encode(['method' => 'cash_on_delivery'])
    ]);

    // Send notifications
    $preorders = Preorder::whereIn('id', $preorder_ids)->get();
    foreach ($preorders as $preorder) {
        if (class_exists('App\Utility\PreorderNotificationUtility')) {
            \App\Utility\PreorderNotificationUtility::preorderNotification($preorder, 'prepayment_confirmed');
        }
    }

    // Clear session
    session()->forget(['preorder_ids', 'preorder_total']);

    flash(translate('Your order has been placed successfully!'))->success();
    // এখানে order_confirmed রাউটে রিডাইরেক্ট করা যেতে পারে
    return redirect()->route('preorder.payment_success');
}

}
