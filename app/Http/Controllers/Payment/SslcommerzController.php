<?php

namespace App\Http\Controllers\Payment;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WalletController;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Models\Order;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use App\Models\Preorder;
use Session;
use Auth;
// Mohammad Hassan - Import the official SSLCommerz library
use App\Library\SslCommerz\SslCommerzNotification;

session_start();

class SslcommerzController extends Controller
{
  public function pay(Request $request)
    {
        if (!Session::has('payment_type')) {
            flash(translate('Payment session has expired. Please try again.'))->error();
            return redirect()->route('home');
        }
    
        $user = Auth::user();
        $paymentType = Session::get('payment_type');
        $paymentData = $request->session()->get('payment_data');
        $customer_info = [];
        $total_amount = 0;
        $num_of_item = 1;

        if ($paymentType == 'cart_payment') {
            // --- সাধারণ চেকআউট (/checkout) এর জন্য লজিক ---
            $customer_info = Session::get('shipping_info_for_order');
            $carts = $user 
                ? Cart::where('user_id', $user->id)->active()->get()
                : Cart::where('temp_user_id', $request->session()->get('temp_user_id'))->active()->get();

            if ($carts->isEmpty() || !$customer_info) {
                flash(translate('Your cart is empty or shipping info is missing.'))->warning();
                return redirect()->route('cart');
            }
            
            foreach ($carts as $cartItem) {
                $product = \App\Models\Product::find($cartItem['product_id']);
                $total_amount += (cart_product_price($cartItem, $product, false, false) + cart_product_tax($cartItem, $product, false)) * $cartItem['quantity'];
                $total_amount += $cartItem['shipping_cost'];
            }
            $num_of_item = $carts->count();

        } elseif ($paymentType == 'preorder_payment') {
            // --- প্রি-অর্ডার (/preorder/payment-selection) এর জন্য লজিক ---
            $total_amount = $paymentData['amount'] ?? 0;
            $preorderIds = $paymentData['preorder_ids'] ?? Session::get('preorder_ids');
            if ($preorderIds) {
                $first_preorder = Preorder::whereIn('id', is_array($preorderIds) ? $preorderIds : [$preorderIds])->first();
                if ($first_preorder) {
                    $customer_info['name'] = $first_preorder->shipping_name;
                    $customer_info['email'] = $first_preorder->shipping_email;
                    $customer_info['phone'] = $first_preorder->shipping_phone;
                    $customer_info['address'] = $first_preorder->shipping_address;
                    $customer_info['city'] = $first_preorder->shipping_city;
                    $customer_info['postal_code'] = $first_preorder->shipping_postal_code ?? '1234';
                }
            }
        } else {
            flash(translate('Unsupported payment type.'))->error();
            return redirect()->route('home');
        }
    
        if ($total_amount <= 0) {
            flash(translate('Invalid payment amount. Please try again.'))->error();
            return redirect()->route('home');
        }

        $post_data = array();
        $post_data['currency'] = "BDT";
        $post_data['tran_id'] = uniqid();
        $post_data['total_amount'] = $total_amount;
        
        $post_data['value_a'] = $post_data['tran_id'];
        $post_data['value_b'] = $paymentType;
        $post_data['value_c'] = $user ? $user->id : 0;
    
        $post_data['product_category'] = 'eCommerce';
        $post_data['product_name'] = 'Online Order';
        $post_data['product_profile'] = 'general';
    
        $post_data['cus_name'] = $customer_info['name'] ?? 'Guest Customer';
        $post_data['cus_email'] = $customer_info['email'] ?? 'guest@example.com';
        $post_data['cus_add1'] = $customer_info['address'] ?? 'N/A';
        $post_data['cus_city'] = $customer_info['city'] ?? 'N/A';
        $post_data['cus_country'] = 'Bangladesh';
        $post_data['cus_phone'] = $customer_info['phone'] ?? '01000000000';
        $post_data['cus_postcode'] = $customer_info['postal_code'] ?? '1234';
        
        $server_name = $request->root() . "/";
        $post_data['success_url'] = $server_name . "sslcommerz/success";
        $post_data['fail_url'] = $server_name . "sslcommerz/fail";
        $post_data['cancel_url'] = $server_name . "sslcommerz/cancel";
        
        $post_data['shipping_method'] = 'YES';
        $post_data['num_of_item'] = $num_of_item;
        $post_data['ship_name'] = $customer_info['name'] ?? 'Guest Customer';
        $post_data['ship_add1'] = $customer_info['address'] ?? 'N/A';
        $post_data['ship_city'] = $customer_info['city'] ?? 'N/A';
        $post_data['ship_country'] = 'Bangladesh';
        $post_data['ship_postcode'] = $customer_info['postal_code'] ?? '1234';
    
        $sslc = new SslCommerzNotification();
        $payment_options = $sslc->makePayment($post_data, 'hosted');
        
        if (!is_array($payment_options)) {
            print_r($payment_options);
        }
    }

    public function success(Request $request)
    {
        $sslc = new SslCommerzNotification();
        $payment = $request->all();
        $tran_id = $payment['tran_id'] ?? null;
        $amount = $payment['amount'] ?? null;
        $currency = $payment['currency'] ?? null;
        $validation = $sslc->orderValidate($payment, $tran_id, $amount, $currency);
        
        if ($validation == TRUE) {
            $payment_type = $request->value_b;
            if ($payment_type == 'cart_payment') {
                return (new CheckoutController)->checkout_done($payment);
            }
            elseif ($payment_type == 'preorder_payment') {
                // প্রি-অর্ডারের সফল পেমেন্টের জন্য সঠিক কন্ট্রোলার এবং মেথড কল করুন
                // return (new PreorderController)->payment_success($payment);
            }
        }
        
        flash(translate('Payment validation failed'))->error();
        return redirect()->route('checkout');
    }

    public function fail(Request $request)
    {
        flash(translate('Payment has failed. Please try again.'))->error();
        return redirect()->route('checkout');
    }

    public function cancel(Request $request)
    {
        flash(translate('Payment has been cancelled.'))->warning();
        return redirect()->route('checkout');
    }
    public function ipn(Request $request)
    {
        #Received all the payement information from the gateway
        if ($request->input('tran_id')) #Check transation id is posted or not.
        {

            $tran_id = $request->input('tran_id');

            
            $combined_order = CombinedOrder::findOrFail($request->session()->get('combined_order_id'));

            if ($order->payment_status == 'Pending') {
                $sslc = new SSLCommerz();
                $validation = $sslc->orderValidate($tran_id, $order->grand_total, 'BDT', $request->all());
                if ($validation == TRUE) {
                    
                    echo "Transaction is successfully Complete";
                } else {
                   

                    echo "validation Fail";
                }
            }
        } else {
            echo "Inavalid Data";
        }
    }
}
