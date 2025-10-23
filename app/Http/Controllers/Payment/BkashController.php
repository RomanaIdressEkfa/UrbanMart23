<?php

namespace App\Http\Controllers\Payment;

use Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CustomerPackage;
use App\Models\SellerPackage;
use App\Models\CombinedOrder;
use App\Http\Controllers\CustomerPackageController;
use App\Http\Controllers\SellerPackageController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\CheckoutController;
use App\Models\Cart;
use App\Models\Order;
use Session;

class BkashController extends Controller
{
    private $base_url;
    public function __construct()
    {
        if (get_setting('bkash_sandbox', 1)) {
            $this->base_url = "https://tokenized.sandbox.bka.sh/v1.2.0-beta/tokenized/";
        } else {
            $this->base_url = "https://tokenized.pay.bka.sh/v1.2.0-beta/tokenized/";
        }
    }

    // public function pay()
    // {
    //     $amount = 0;
    //     if (Session::has('payment_type')) {
    //         $paymentType = Session::get('payment_type');
    //         $paymentData = Session::get('payment_data');
    //         if ($paymentType == 'cart_payment') {
    //             $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
    //             $amount = round($combined_order->grand_total);
    //         } elseif ($paymentType == 'order_re_payment') {
    //             $order = Order::findOrFail($paymentData['order_id']);
    //             $amount = round($order->grand_total);
    //         } elseif ($paymentType == 'wallet_payment') {
    //             $amount = round($paymentData['amount']);
    //         } elseif ($paymentType == 'customer_package_payment') {
    //             $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
    //             $amount = round($customer_package->amount);
    //         } elseif ($paymentType == 'seller_package_payment') {
    //             $seller_package = SellerPackage::findOrFail($paymentData['seller_package_id']);
    //             $amount = round($seller_package->amount);
    //         } elseif ($paymentType == 'preorder_payment') {
    //             $amount = round(($paymentData['amount'] ?? 0));
    //         }
    //     }

    //     Session::forget('bkash_token');
    //     Session::put('bkash_token', $this->getToken());
    //     Session::put('amount', $amount);
    //     return redirect()->route('bkash.create_payment');
    // }

    
    
    public function pay()
{
    $amount = 0;
    if (Session::has('payment_type')) {
        $paymentType = Session::get('payment_type');
        $paymentData = Session::get('payment_data');
        
        if ($paymentType == 'cart_payment') {
            // --- START: সংশোধিত এবং সঠিক কোড ---
            // সরাসরি কার্ট থেকে মোট মূল্য গণনা করা হচ্ছে
            $user = Auth::user();
            $carts = $user 
                ? Cart::where('user_id', $user->id)->active()->get()
                : Cart::where('temp_user_id', Session::get('temp_user_id'))->active()->get();

            if($carts->isEmpty()){
                flash(translate('Your cart is empty.'))->warning();
                return redirect()->route('cart');
            }

            $total = 0;
            foreach ($carts as $cartItem) {
                $product = \App\Models\Product::find($cartItem['product_id']);
                $total += (cart_product_price($cartItem, $product, false, false) + cart_product_tax($cartItem, $product, false)) * $cartItem['quantity'];
                $total += $cartItem['shipping_cost'];
            }
            $amount = round($total);
            // --- END: সংশোধিত এবং সঠিক কোড ---

        } elseif ($paymentType == 'order_re_payment') {
            $order = Order::findOrFail($paymentData['order_id']);
            $amount = round($order->grand_total);
        } elseif ($paymentType == 'wallet_payment') {
            $amount = round($paymentData['amount']);
        } elseif ($paymentType == 'customer_package_payment') {
            $customer_package = CustomerPackage::findOrFail($paymentData['customer_package_id']);
            $amount = round($customer_package->amount);
        } elseif ($paymentType == 'seller_package_payment') {
            $seller_package = SellerPackage::findOrFail($paymentData['seller_package_id']);
            $amount = round($seller_package->amount);
        } elseif ($paymentType == 'preorder_payment') {
            // প্রি-অর্ডারের জন্য আগের লজিক অপরিবর্তিত আছে
            $amount = round(($paymentData['amount'] ?? 0));
        }
    }

    if ($amount <= 0) {
        flash(translate('Invalid payment amount.'))->error();
        return redirect()->route('home');
    }

    Session::forget('bkash_token');
    Session::put('bkash_token', $this->getToken());
    Session::put('amount', $amount);
    return redirect()->route('bkash.create_payment');
}
    
    
    public function create_payment()
    {
        $requestbody = array(
            'mode' => '0011',
            'payerReference' => ' ',
            'callbackURL' => route('bkash.callback'),
            'amount' => Session::get('amount'),
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => "Inv" . Date('YmdH') . rand(1000, 10000)
        );
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . Session::get('bkash_token'),
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        $url = curl_init($this->base_url . 'checkout/create');
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);
        $decoded = json_decode($resultdata);
        // Guard: if no bkashURL returned, show fail page instead of redirecting
        if (!$decoded || !isset($decoded->bkashURL) || empty($decoded->bkashURL)) {
            \Log::error('bKash create_payment did not return bkashURL', ['response' => $decoded, 'raw' => $resultdata]);
            return view('frontend.bkash.fail')->with(['errorMessage' => 'Unable to initialize bKash payment']);
        }
        return redirect($decoded->bkashURL);
    }

    public function getToken()
    {
        $request_data = array('app_key' => env('BKASH_CHECKOUT_APP_KEY'), 'app_secret' => env('BKASH_CHECKOUT_APP_SECRET'));
        $request_data_json = json_encode($request_data);

        $header = array(
            'Content-Type:application/json',
            'username:' . env('BKASH_CHECKOUT_USER_NAME'),
            'password:' . env('BKASH_CHECKOUT_PASSWORD')
        );

        $url = curl_init($this->base_url . 'checkout/token/grant');
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $request_data_json);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $resultdata = curl_exec($url);
        $curl_error = curl_error($url);
        $http_code = curl_getinfo($url, CURLINFO_HTTP_CODE);
        curl_close($url);

        // Mohammad Hassan - Enhanced error logging
        \Log::info('bKash Token Request Details', [
            'url' => $this->base_url . 'checkout/token/grant',
            'request_data' => $request_data,
            'http_code' => $http_code,
            'curl_error' => $curl_error,
            'raw_response' => $resultdata
        ]);

        $response = json_decode($resultdata);
        
        // Check if the response is valid and contains the token
        if (!$response || !isset($response->id_token)) {
            // Enhanced error logging
            \Log::error('bKash Token Error Details', [
                'http_code' => $http_code,
                'curl_error' => $curl_error,
                'raw_response' => $resultdata,
                'parsed_response' => $response,
                'credentials_check' => [
                    'app_key' => env('BKASH_CHECKOUT_APP_KEY') ? 'SET' : 'NOT SET',
                    'app_secret' => env('BKASH_CHECKOUT_APP_SECRET') ? 'SET' : 'NOT SET',
                    'username' => env('BKASH_CHECKOUT_USER_NAME') ? 'SET' : 'NOT SET',
                    'password' => env('BKASH_CHECKOUT_PASSWORD') ? 'SET' : 'NOT SET'
                ]
            ]);
            throw new \Exception('Failed to get bKash token. Please check your bKash credentials.');
        }

        $token = $response->id_token;
        return $token;
    }

    public function callback(Request $request)
    {
        // Mohammad Hassan - Add debugging for bKash callback
        \Log::info('bKash Callback Received', [
            'request_data' => $request->all(),
            'session_data' => [
                'payment_type' => Session::get('payment_type'),
                'payment_data' => Session::get('payment_data'),
                'combined_order_id' => Session::get('combined_order_id'),
                'amount' => Session::get('amount')
            ]
        ]);
        
        $allRequest = $request->all();
        if (isset($allRequest['status']) && $allRequest['status'] == 'success'){
            \Log::info('bKash Payment Status: Success', ['paymentID' => $allRequest['paymentID'] ?? 'unknown']);
            
            $resultdata = $this->execute($allRequest['paymentID']);
            if (!$resultdata){
                $resultdata = $this->query($allRequest['paymentID']);
            }

            Session::forget('payment_details');
            Session::put('payment_details', $resultdata);
            $response = json_decode($resultdata, true);
            
            // Mohammad Hassan - Log payment execution result
            \Log::info('bKash Payment Execution Result', [
                'paymentID' => $allRequest['paymentID'] ?? 'unknown',
                'response' => $response
            ]);

            if (isset($response['statusCode']) && $response['statusCode'] == "0000" && $response['transactionStatus'] == "Completed") {
                \Log::info('bKash Payment Completed Successfully', ['paymentID' => $allRequest['paymentID'] ?? 'unknown']);
                return redirect()->route('bkash.success');
            } else if (isset($response['transactionStatus']) && $response['transactionStatus'] == "Initiated") {
                // Break potential redirect loop by showing a fail page with guidance
                \Log::warning('bKash Payment is Initiated. Not redirecting again to avoid loop.', ['paymentID' => $allRequest['paymentID'] ?? 'unknown', 'response' => $response]);
                return view('frontend.bkash.fail')->with(['errorMessage' => 'Payment Initiated. Please complete payment on bKash page or try again.']);
            }
            
            \Log::error('bKash Payment Failed', [
                'paymentID' => $allRequest['paymentID'] ?? 'unknown',
                'statusMessage' => $response['statusMessage'] ?? 'Unknown error'
            ]);
            return view('frontend.bkash.fail')->with(['errorMessage' => $response['statusMessage']]);
            
        } else if (isset($allRequest['status']) && $allRequest['status'] == 'cancel'){
            \Log::warning('bKash Payment Cancelled by User', $allRequest);
            return view('frontend.bkash.fail')->with(['errorMessage' => 'Payment Cancelled']);
        } else{
            \Log::error('bKash Payment Failed', $allRequest);
            return view('frontend.bkash.fail')->with(['errorMessage' => 'Payment Failure']);
        }
        
        // $allRequest = $request->all();
        // if (isset($allRequest['status']) && $allRequest['status'] == 'failure') {
        //     return view('frontend.bkash.fail')->with([
        //         'errorMessage' => 'Payment Failure'
        //     ]);
        // } else if (isset($allRequest['status']) && $allRequest['status'] == 'cancel') {
        //     return view('frontend.bkash.fail')->with([
        //         'errorMessage' => 'Payment Cancelled'
        //     ]);
        // } else {

        //     $resultdata = $this->execute($allRequest['paymentID']);
        //     Session::forget('payment_details');
        //     Session::put('payment_details', $resultdata);

        //     $result_data_array = json_decode($resultdata, true);
        //     if (array_key_exists("statusCode", $result_data_array) && $result_data_array['statusCode'] != '0000') {
        //         return view('frontend.bkash.fail')->with([
        //             'errorMessage' => $result_data_array['statusMessage'],
        //         ]);
        //     } else if (array_key_exists("statusMessage", $result_data_array)) {
        //         // if execute api failed to response
        //         sleep(1);
        //         $resultdata = json_decode($this->query($allRequest['paymentID']));

        //         if ($resultdata->transactionStatus == 'Initiated') {
        //             return redirect()->route('bkash.create_payment');
        //         }
        //     }

        //     return redirect()->route('bkash.success');
        // }
    }

    public function execute($paymentID)
    {

        $auth = Session::get('bkash_token');

        $requestbody = array(
            'paymentID' => $paymentID
        );
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        $url = curl_init($this->base_url . 'checkout/execute');
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }

    public function query($paymentID)
    {

        $auth = Session::get('bkash_token');

        $requestbody = array(
            'paymentID' => $paymentID
        );
        $requestbodyJson = json_encode($requestbody);

        $header = array(
            'Content-Type:application/json',
            'Authorization:' . $auth,
            'X-APP-Key:' . env('BKASH_CHECKOUT_APP_KEY')
        );

        $url = curl_init($this->base_url . 'checkout/payment/status');
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $requestbodyJson);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($url, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultdata = curl_exec($url);
        curl_close($url);

        return $resultdata;
    }


    public function success(Request $request)
    {
        // Mohammad Hassan - Add debugging for bKash success
        \Log::info('bKash Success Method Called', [
            'session_data' => [
                'payment_type' => Session::get('payment_type'),
                'payment_data' => Session::get('payment_data'),
                'payment_details' => Session::get('payment_details'),
                'combined_order_id' => Session::get('combined_order_id')
            ]
        ]);
        
        $payment_type = Session::get('payment_type');
        $paymentData = Session::get('payment_data');
        
        \Log::info('bKash Processing Payment Type', ['payment_type' => $payment_type]);
        
        if ($payment_type == 'cart_payment') {
            \Log::info('bKash Cart Payment - Calling checkout_done', ['combined_order_id' => Session::get('combined_order_id')]);
            return (new CheckoutController)->checkout_done(Session::get('combined_order_id'), Session::get('payment_details'));
        }
        elseif ($payment_type == 'order_re_payment') {
            \Log::info('bKash Order Re-payment', ['payment_data' => $paymentData]);
            return (new CheckoutController)->orderRePaymentDone($paymentData, Session::get('payment_details'));
        }
        elseif ($payment_type == 'wallet_payment') {
            \Log::info('bKash Wallet Payment', ['payment_data' => $paymentData]);
            return (new WalletController)->wallet_payment_done($paymentData, Session::get('payment_details'));
        }
        elseif ($payment_type == 'customer_package_payment') {
            \Log::info('bKash Customer Package Payment', ['payment_data' => $paymentData]);
            return (new CustomerPackageController)->purchase_payment_done($paymentData, Session::get('payment_details'));
        }
        elseif ($payment_type == 'seller_package_payment') {
            \Log::info('bKash Seller Package Payment', ['payment_data' => $paymentData]);
            return (new SellerPackageController)->purchase_payment_done($paymentData, Session::get('payment_details'));
        }
        
        \Log::error('bKash Success: Unknown payment type', ['payment_type' => $payment_type]);
    }
}
