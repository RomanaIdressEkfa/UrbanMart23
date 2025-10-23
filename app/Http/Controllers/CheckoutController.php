<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Address;
use App\Models\Carrier;
use App\Models\CombinedOrder;
use App\Models\Country;
use App\Models\Product;
use App\Models\User;
use App\Models\PickupPoint;
use App\Utility\EmailUtility;
use App\Utility\NotificationUtility;
use Session;
use Auth;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Mail;
use Exception; // Exception ক্লাসটি import করুন

class CheckoutController extends Controller
{

    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {
        // ... এই মেথডটি অপরিবর্তিত থাকবে ...
        $guest_checkout_enabled = (int) (get_setting('guest_checkout_activation') ?? 0) === 1 || (int) (get_setting('guest_checkout_active') ?? 0) === 1;
        if(!$guest_checkout_enabled && auth()->user() == null){
            return redirect()->route('user.login');
        }
        if(auth()->check() && !$request->user()->hasVerifiedEmail()){
            return redirect()->route('verification.notice');
        }
        $carts = auth()->check() 
            ? Cart::where('user_id', Auth::user()->id)->active()->get()
            : Cart::where('temp_user_id', $request->session()->get('temp_user_id'))->active()->get();
        if ($carts->isEmpty()) {
            flash(translate('Your cart is empty'))->warning();
            return redirect()->route('cart');
        }
        $addresses = collect();
        $address_id = 0;
        $shipping_info = [];
        if (auth()->check()) {
            $user_id = Auth::user()->id;
            $addresses = Address::where('user_id', $user_id)->with('city')->latest()->get();
            if($addresses->isNotEmpty()){
                $default_address = $addresses->where('set_default', 1)->first();
                $address = $default_address ?: $addresses->first();
                $address_id = $address->id;
                $shipping_info['country_id'] = $address->country_id;
                $shipping_info['city_id'] = $address->city_id;
                $shipping_info['area_id'] = $address->area_id;
            }
        }
        $total = 0;
        $tax = 0;
        $shipping = 0;
        $subtotal = 0;
        $carts->toQuery()->update(['address_id' => $address_id]);
        $carts = $carts->fresh();
        $carrier_list = [];
        $default_shipping_type = 'home_delivery';
        foreach ($carts as $key => $cartItem) {
            $product = Product::find($cartItem['product_id']);
            $tax += cart_product_tax($cartItem, $product, false) * $cartItem['quantity'];
            $subtotal += cart_product_price($cartItem, $product, false, false) * $cartItem['quantity'];
            $cartItem['shipping_cost'] = getShippingCost($carts, $key, $shipping_info);
            $cartItem['shipping_type'] = $default_shipping_type;
            $shipping += $cartItem['shipping_cost'];
            $cartItem->save();
        }
        $total = $subtotal + $tax + $shipping;
        $has_preorder_products = $carts->some(function ($cartItem) {
            return optional($cartItem->product)->isOutOfStock() && optional($cartItem->product)->isPreorderAvailable();
        });
        $pickup_point_list = (get_setting('pickup_point') == 1) ? PickupPoint::where('pick_up_status', 1)->get() : null;
        return view('frontend.checkout', compact('carts', 'addresses', 'address_id', 'total', 'carrier_list', 'shipping_info', 'has_preorder_products', 'pickup_point_list'));
    }

    public function checkout(Request $request)
    {
        try { 
            if ($request->payment_option == null) {
                flash(translate('Select Payment Option'))->warning();
                return back();
            }

            $shipping_address_data = [
                'name'      => $request->name,
                'email'     => $request->email,
                'address'   => $request->address,
                'country'   => 'Bangladesh',
                'city'      => \App\Models\City::find($request->city_id)->name ?? '',
                'phone'     => $request->phone,
            ];
            $request->session()->put('shipping_info_for_order', $shipping_address_data);

            if ($request->payment_option === 'cash_on_delivery') {
                (new OrderController)->store($request);
                $combined_order_id = $request->session()->get('combined_order_id');
                if (!$combined_order_id) {
                    throw new Exception('Order creation failed for offline payment.');
                }
                $combined_order = CombinedOrder::findOrFail($combined_order_id);
                foreach ($combined_order->orders as $order) {
                    $order->payment_status = 'unpaid';
                    $order->save();
                }
                if (auth()->check()) {
                    Cart::where('user_id', auth()->id())->delete();
                } else {
                    Cart::where('temp_user_id', $request->session()->get('temp_user_id'))->delete();
                }
                flash(translate('Your order has been placed successfully.'))->success();
                return redirect()->route('order_confirmed');
            }
            else { // --- সকল অনলাইন পেমেন্টের জন্য (Bkash, Sslcommerz) ---
                $request->session()->put('payment_type', 'cart_payment');
                $request->session()->put('payment_data', $request->all());

                $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $request->payment_option))) . "Controller";
                if (class_exists($decorator)) {
                    return (new $decorator)->pay($request);
                }
                
                throw new Exception('Payment gateway not found.');
            }

        } catch (Exception $e) {
            flash($e->getMessage())->error();
            return back();
        }
    }
    
    public function checkout_done($payment)
    {
        $request_data = Session::get('payment_data', []);
        if(empty($request_data)){
            flash(translate('Your session has expired. Please try again.'))->error();
            return redirect()->route('home');
        }
        $request = new Request($request_data);

        (new OrderController)->store($request);
        $combined_order_id = Session::get('combined_order_id');
        if (!$combined_order_id) {
            \Log::error('CRITICAL: Payment successful but order creation failed. Payment Details: ' . json_encode($payment));
            flash(translate('Payment was successful but we could not create your order. Please contact support.'))->error();
            return redirect()->route('home');
        }
        
        $combined_order = CombinedOrder::findOrFail($combined_order_id);

        foreach ($combined_order->orders as $order) {
            $order->payment_status = 'paid';
            $order->payment_details = json_encode($payment);
            $order->save();

            EmailUtility::order_email($order, 'paid');
            calculateCommissionAffilationClubPoint($order);
        }

        if (auth()->check()) {
            Cart::where('user_id', auth()->id())->delete();
        } else {
            Cart::where('temp_user_id', session()->get('temp_user_id'))->delete();
        }
        
        Session::forget('payment_data');
        Session::put('combined_order_id', $combined_order_id);
        
        flash(translate('Payment completed successfully!'))->success();
        return redirect()->route('order_confirmed');
    }

    public function order_confirmed()
    {
        if(!Session::has('combined_order_id')){
            flash(translate('Invalid access to order confirmation page.'))->error();
            return redirect()->route('home');
        }
        $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
        Session::forget('combined_order_id');
        foreach($combined_order->orders as $order){
            if($order->notified == 0){
                NotificationUtility::sendOrderPlacedNotification($order);
                $order->notified = 1;
                $order->save();
            }
        }
        return view('frontend.order_confirmed', compact('combined_order'));
    }
    
    protected function createUser($data)
    {
        try {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::random(10)),
                'phone' => $data['phone'] ?? null,
                'email_verified_at' => now(),
                'verification_token' => null,
                'is_verified' => 1
            ]);
        
            Auth::login($user);
            Session::put('user_id', $user->id);
            
            Cart::where('temp_user_id', Session::get('temp_user_id'))
                ->update([
                    'user_id' => $user->id,
                    'temp_user_id' => null
                ]);
            
            return $user;
        } catch (Exception $e) {
            flash(translate('Could not create user. Please try again.'))->error();
            return null;
        }
    }

    // public function checkout_done($combined_order_id, $payment)
    // {
    //     $combined_order = CombinedOrder::findOrFail($combined_order_id);

    //     foreach ($combined_order->orders as $key => $order) {
    //         $order = Order::findOrFail($order->id);
    //         $order->payment_status = 'paid';
    //         $order->payment_details = $payment;
    //         $order->save();

    //         EmailUtility::order_email($order, 'paid');
    //         calculateCommissionAffilationClubPoint($order);
    //     }

    //     // --- পরিবর্তন ৩: শুধুমাত্র পেমেন্ট সফল হওয়ার পর এখানে কার্ট খালি করা হচ্ছে ---
    //     if ($combined_order->user_id != null) {
    //         Cart::where('user_id', $combined_order->user_id)->delete();
    //     } else {
    //         // অতিথি ব্যবহারকারীর জন্য temp_user_id ব্যবহার করে কার্ট খালি করা
    //         $temp_user_id = session()->get('temp_user_id');
    //         if($temp_user_id){
    //             Cart::where('temp_user_id', $temp_user_id)->delete();
    //         }
    //     }

    //     Session::put('combined_order_id', $combined_order_id);
    //     return redirect()->route('order_confirmed');
    // }

    // public function order_confirmed()
    // {
    //     if(!Session::has('combined_order_id')){
    //         flash(translate('Invalid access to order confirmation page.'))->error();
    //         return redirect()->route('home');
    //     }

    //     $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));

    //     Session::forget('club_point');
    //     Session::forget('combined_order_id');

    //     foreach($combined_order->orders as $order){
    //         if($order->notified == 0){
    //             NotificationUtility::sendOrderPlacedNotification($order);
    //             $order->notified = 1;
    //             $order->save();
    //         }
    //     }

    //     return view('frontend.order_confirmed', compact('combined_order'));
    // }

    

    public function apply_coupon_code(Request $request)
    {
        if(!auth()->check()) {
            flash(translate('You must be logged in to apply a coupon.'))->warning();
            $carts = Cart::where('temp_user_id', Session::get('temp_user_id'))->active()->get();
            $html = view('frontend.partials.cart.cart_summary', ['carts' => $carts, 'coupon' => null, 'proceed' => 0])->render();
            return response()->json([
                'response_message' => ['response' => 'warning', 'message' => translate('You must be logged in to apply a coupon.')],
                'html' => $html
            ]);
        }
        
        // ... (rest of your coupon logic)
    }

    public function remove_coupon_code(Request $request)
    {
        $user = auth()->user();
        if(!$user){
            return back();
        }
        
        Cart::where('user_id', $user->id)
            ->update([
                'discount' => 0.00,
                'coupon_code' => '',
                'coupon_applied' => 0
            ]);

        $carts = Cart::where('user_id', $user->id)->active()->get();
        return view('frontend.partials.cart.cart_summary', ['carts' => $carts, 'coupon' => null, 'proceed' => 0]);
    }
    
    public function updateDeliveryAddress(Request $request)
    {
        $user = auth()->user();
        $carts = $user 
            ? Cart::where('user_id', $user->id)->active()->get()
            : Cart::where('temp_user_id', $request->session()->get('temp_user_id'))->active()->get();

        if($carts->isEmpty()){
            return response()->json([], 404);
        }

        $carts->toQuery()->update(['address_id' => $request->address_id]);
        
        $address = $user ? Address::find($request->address_id) : null;

        $shipping_info = [
            'country_id' => $address->country_id ?? ($request->address_id ?? 0), // For guest, address_id is country_id
            'city_id'    => $address->city_id ?? ($request->city_id ?? 0),
            'area_id'    => $address->area_id ?? ($request->area_id ?? 0),
        ];
        
        foreach ($carts as $key => $cartItem) {
            $cartItem->shipping_cost = getShippingCost($carts, $key, $shipping_info);
            $cartItem->save();
        }

        $carts = $carts->fresh();

        return [
            'delivery_info' => view('frontend.partials.cart.delivery_info', ['carts' => $carts, 'shipping_info' => $shipping_info, 'carrier_list' => []])->render(),
            'cart_summary' => view('frontend.partials.cart.cart_summary', ['carts' => $carts, 'proceed' => 0])->render(),
            'carrier_count' => 0
        ];
    }
}