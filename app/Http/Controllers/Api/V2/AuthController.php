<?php

/** @noinspection PhpUndefinedClassInspection */

namespace App\Http\Controllers\Api\V2;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Http\Controllers\OTPVerificationController;
use App\Mail\GuestAccountOpeningMailManager;
use App\Mail\VerifyEmail;
use App\Models\Address;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\AppEmailVerificationNotification;
use Hash;
use Socialite;
use App\Models\Cart;
use App\Rules\Recaptcha;
use App\Utility\EmailUtility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\PersonalAccessToken;
// use Mail;

class AuthController extends Controller
{
    /**
     * Check if email belongs to admin or seller
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUserType(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json([
                'result' => true,
                'user_type' => $user->user_type
            ]);
        }

        return response()->json([
            'result' => false,
            'user_type' => null
        ]);
    }

  public function userEmailSubmit(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $code = rand(100000, 999999);

        $user = User::updateOrCreate(
            ['email' => $request->email],
            ['verification_token' => $code, 'user_type' => 'user']
        );

        // এখানে তোমার Mail logic বসাও
        // Mail::to($request->email)->send(new VerificationCodeMail($code));

        return response()->json([
            'result' => true,
            'message' => 'Verification code sent successfully.'
        ]);
    }

    /**
     * Handle Google OAuth login
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function googleLogin(Request $request)
    {
        try {
            $request->validate([
                'access_token' => 'required|string'
            ]);

            // Get user info from Google using access token
            $googleUser = Socialite::driver('google')->userFromToken($request->access_token);

            if (!$googleUser) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid Google access token'
                ], 400);
            }

            // Check if user exists by provider_id
            $existingUser = User::where('provider_id', $googleUser->id)
                ->where('provider', 'google')
                ->first();

            if ($existingUser) {
                // Update access token and login
                $existingUser->access_token = $request->access_token;
                $existingUser->save();
                $user = $existingUser;
            } else {
                // Check if user exists by email
                $existingUserByEmail = User::where('email', $googleUser->email)->first();

                if ($existingUserByEmail) {
                    // Link Google account to existing user
                    $existingUserByEmail->provider_id = $googleUser->id;
                    $existingUserByEmail->provider = 'google';
                    $existingUserByEmail->access_token = $request->access_token;
                    $existingUserByEmail->email_verified_at = now();
                    $existingUserByEmail->save();
                    $user = $existingUserByEmail;
                } else {
                    // Create new user
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'provider_id' => $googleUser->id,
                        'provider' => 'google',
                        'access_token' => $request->access_token,
                        'user_type' => 'customer',
                        'email_verified_at' => now(),
                        'password' => bcrypt(\Illuminate\Support\Str::random(8)) // Random password
                    ]);
                }
            }

            // Generate access token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'result' => true,
                'message' => 'Google login successful!',
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Google login failed. Please try again.'
            ], 500);
        }
     }

    /**
     * Handle wholesaler registration
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function wholesalerRegister(Request $request)
    {
        try {
            $request->validate([
                'businessName' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'address' => 'required|string',
                'password' => 'required|string|min:8',
                'confirmPassword' => 'required|string|same:password',
                'facebookLink' => 'nullable|url',
                'websiteLink' => 'nullable|url',
                'tradeLicense' => 'nullable|string|max:100'
            ]);

            // Create wholesaler user
            $user = User::create([
                'name' => $request->businessName,
                'email' => $request->email,
                'phone' => $request->phone,
                'user_type' => 'wholesaler',
                'business_name' => $request->businessName,
                'facebook_link' => $request->facebookLink,
                'website_link' => $request->websiteLink,
                'address' => $request->address,
                'trade_license' => $request->tradeLicense,
                'password' => bcrypt($request->password),
                'status' => 'pending',
                'email_verified_at' => now() // Auto-verify for wholesalers
            ]);

            return response()->json([
                'result' => true,
                'message' => 'Wholesaler registration successful! Your account is pending approval.',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'status' => $user->status
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // --- শুরু: ভ্যালিডেশন ফেইল হলে এরর রেসপন্স পাঠানো ---
            return response()->json([
                'result' => false,
                'message' => $e->errors()
            ], 422);
            // --- শেষ: ভ্যালিডেশন ফেইল হলে এরর রেসপন্স পাঠানো ---
        } catch (\Exception $e) {
            // --- শুরু: অন্য কোনো সমস্যা হলে এরর রেসপন্স পাঠানো ---
            return response()->json([
                'result' => false,
                'message' => 'Registration failed. Please try again. Error: ' . $e->getMessage()
            ], 500);
            // --- শেষ: অন্য কোনো সমস্যা হলে এরর রেসপন্স পাঠানো ---
        }
    }

    /**
     * Handle wholesaler login
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function wholesalerLogin(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required|string'
            ]);

            // Find wholesaler user
            $user = User::where('email', $request->email)
                ->where('user_type', 'wholesaler')
                ->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid email or password. Please check your credentials.',
                    'message_type' => 'error'
                ], 401);
            }

            // Check if account is approved
             if ($user->status === 'pending') {
                return response()->json([
                    'result' => false,
                    'message' => 'Your wholesaler account is still pending approval. Please contact support.',
                    'message_type' => 'warning'
                ], 403);
            }

               if ($user->status !== 'active') { // 'active' মানে 'approved' ধরা হয়েছে। অন্য কোনো স্ট্যাটাস (যেমন inactive, rejected) থাকলে লগইন করতে দেওয়া হবে না।
                return response()->json([
                    'result' => false,
                    'message' => 'Your account is not active. Please contact support.',
                    'message_type' => 'error'
                ], 403);
            }

            // Mohammad Hassan
            // Log in the user to create Laravel session
            \Auth::login($user);
            
            // Set session flash message for dashboard
            session()->flash('success', 'Welcome back! You have successfully logged in as a wholesaler.');
            
            // Generate access token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'result' => true,
                'message' => 'Welcome back! Login successful. Redirecting to your dashboard...',
                'message_type' => 'success',
                'access_token' => $token,
                'redirect_url' => route('dashboard'),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'user_type' => $user->user_type,
                    'status' => $user->status
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'result' => false,
                'message' => 'Please fill in all required fields correctly.',
                'message_type' => 'error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Login failed. Please try again later.',
                'message_type' => 'error'
            ], 500);
        }
    }

    public function userVerifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required'
        ]);

        $user = User::where('email', $request->email)
                    ->where('verification_token', $request->code)
                    ->first();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => 'Invalid verification code.'
            ], 400);
        }

        $user->is_verified = true;
        $user->verification_token = null;
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'result' => true,
            'access_token' => $token,
            'user' => $user
        ]);
    }

    public function resendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $code = rand(100000, 999999);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'result' => false,
                'message' => 'User not found.'
            ], 404);
        }

        $user->verification_token = $code;
        $user->save();

        // Mail::to($request->email)->send(new VerificationCodeMail($code));

        return response()->json([
            'result' => true,
            'message' => 'Verification code resent successfully.'
        ]);
    }

public function loginWithEmail(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $user = User::firstOrCreate(
        ['email' => $request->email],
        [
            'name' => 'User-'.Str::random(5),
            'password' => bcrypt(Str::random(12)),
            'user_type' => 'user',
        ]
    );

    // Generate token
    $token = $user->createToken('api_token')->plainTextToken;
$token = Str::random(40);
$user->verification_token = $token;
$user->save();

// Send email
$verifyUrl = route('verify.email', $token);
Mail::to($user->email)->send(new VerifyEmail($verifyUrl));


    return response()->json([
        'success' => true,
        'message' => 'Login successful',
        'user' => $user,
        'access_token' => $token
    ]);
}

public function signupWholesaler(Request $request)
{
    $request->validate([
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'phone' => 'required',
        'address' => 'required',
        'facebook_url' => 'nullable|url',
        'website_url' => 'nullable|url',
        'trade_license' => 'nullable|string',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'address' => $request->address,
        'facebook_url' => $request->facebook_url,
        'website_url' => $request->website_url,
        'trade_license' => $request->trade_license,
        'password' => bcrypt(Str::random(12)),
        'user_type' => 'wholesaler',
        // Mohammad Hassan - Set email_verified_at to now() for wholesaler
        'email_verified_at' => now(),
    ]);

    $token = $user->createToken('api_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'message' => 'Wholesaler registered successfully',
        'user' => $user,
        'access_token' => $token
    ]);
}

    public function confirmCode(Request $request)
    {
        $user = auth()->user();

        if ($user->verification_code == $request->verification_code) {
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->verification_code = null;
            $user->save();
            return response()->json([
                'result' => true,
                'message' => translate('Your account is now verified'),
            ], 200);
        } else {
            return response()->json([
                'result' => false,
                'message' => translate('Code does not match, you can request for resending the code'),
            ], 200);
        }
    }

    public function login(Request $request)
    {
        $messages = array(
            'email.required' => $request->login_by == 'email' ? translate('Email is required') : translate('Phone is required'),
            'email.email' => translate('Email must be a valid email address'),
            'email.numeric' => translate('Phone must be a number.'),
            'password.required' => translate('Password is required'),
        );
        $validator = Validator::make($request->all(), [
            'password' => 'required',
            'login_by' => 'required',
            'email' => [
                'required',
                Rule::when($request->login_by === 'email', ['email', 'required']),
                Rule::when($request->login_by === 'phone', ['numeric', 'required']),
            ]
        ], $messages);

        if ($validator->fails()) {
            return response()->json([
                'result' => false,
                'message' => $validator->errors()->all()
            ]);
        }

        $delivery_boy_condition = $request->has('user_type') && $request->user_type == 'delivery_boy';
        $seller_condition = $request->has('user_type') && $request->user_type == 'seller';
        $req_email = $request->email;

        if ($delivery_boy_condition) {
            $user = User::whereIn('user_type', ['delivery_boy'])
                ->where(function ($query) use ($req_email) {
                    $query->where('email', $req_email)
                        ->orWhere('phone', $req_email);
                })
                ->first();
        } elseif ($seller_condition) {
            $user = User::whereIn('user_type', ['seller'])
                ->where(function ($query) use ($req_email) {
                    $query->where('email', $req_email)
                        ->orWhere('phone', $req_email);
                })
                ->first();
        } else {
            $user = User::whereIn('user_type', ['customer'])
                ->where(function ($query) use ($req_email) {
                    $query->where('email', $req_email)
                        ->orWhere('phone', $req_email);
                })
                ->first();
        }
        // if (!$delivery_boy_condition) {
        if (!$delivery_boy_condition && !$seller_condition) {
            if (\App\Utility\PayhereUtility::create_wallet_reference($request->identity_matrix) == false) {
                return response()->json(['result' => false, 'message' => 'Identity matrix error', 'user' => null], 401);
            }
        }

        if ($user != null) {
            if (!$user->banned) {
                if (Hash::check($request->password, $user->password)) {
                    if($user->user_type=='seller' && $user->shop->registration_approval  == 0){
                        return response()->json(['result' => false, 'message' => translate('Your seller account is under review. We will notify you once approved.'), 'user' => null], 401);
                    }else{
                        $tempUserId = $request->has('temp_user_id') ? $request->temp_user_id : null;
                        return $this->loginSuccess($user,'', $tempUserId);
                    }

                } else {
                    return response()->json(['result' => false, 'message' => translate('Unauthorized'), 'user' => null], 401);
                }
            } else {
                return response()->json(['result' => false, 'message' => translate('User is banned'), 'user' => null], 401);
            }
        } else {
            return response()->json(['result' => false, 'message' => translate('User not found'), 'user' => null], 401);
        }
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {

        $user = request()->user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged out')
        ]);
    }

    public function socialLogin(Request $request)
    {
        if (!$request->provider) {
            return response()->json([
                'result' => false,
                'message' => translate('User not found'),
                'user' => null
            ]);
        }

        switch ($request->social_provider) {
            case 'facebook':
                $social_user = Socialite::driver('facebook')->fields([
                    'name',
                    'first_name',
                    'last_name',
                    'email'
                ]);
                break;
            case 'google':
                $social_user = Socialite::driver('google')
                    ->scopes(['profile', 'email']);
                break;
            case 'twitter':
                $social_user = Socialite::driver('twitter');
                break;
            case 'apple':
                $social_user = Socialite::driver('sign-in-with-apple')
                    ->scopes(['name', 'email']);
                break;
            default:
                $social_user = null;
        }
        if ($social_user == null) {
            return response()->json(['result' => false, 'message' => translate('No social provider matches'), 'user' => null]);
        }

        if ($request->social_provider == 'twitter') {
            $social_user_details = $social_user->userFromTokenAndSecret($request->access_token, $request->secret_token);
        } else {
            $social_user_details = $social_user->userFromToken($request->access_token);
        }

        if ($social_user_details == null) {
            return response()->json(['result' => false, 'message' => translate('No social account matches'), 'user' => null]);
        }

        $existingUserByProviderId = User::where('provider_id', $request->provider)->first();

        if ($existingUserByProviderId) {
            $existingUserByProviderId->access_token = $social_user_details->token;
            if ($request->social_provider == 'apple') {
                $existingUserByProviderId->refresh_token = $social_user_details->refreshToken;
                if (!isset($social_user->user['is_private_email'])) {
                    $existingUserByProviderId->email = $social_user_details->email;
                }
            }
            $existingUserByProviderId->save();
            return $this->loginSuccess($existingUserByProviderId);
        } else {
            $existing_or_new_user = User::firstOrNew(
                [['email', '!=', null], 'email' => $social_user_details->email]
            );

            // $existing_or_new_user->user_type = 'customer';
            $existing_or_new_user->provider_id = $social_user_details->id;

            if (!$existing_or_new_user->exists) {
                if ($request->social_provider == 'apple') {
                    if ($request->name) {
                        $existing_or_new_user->name = $request->name;
                    } else {
                        $existing_or_new_user->name = 'Apple User';
                    }
                } else {
                    $existing_or_new_user->name = $social_user_details->name;
                }
                $existing_or_new_user->email = $social_user_details->email;
                $existing_or_new_user->email_verified_at = date('Y-m-d H:m:s');
            }

            $existing_or_new_user->save();

            return $this->loginSuccess($existing_or_new_user);
        }
    }

    // Guest user Account Create
    public function guestUserAccountCreate(Request $request)
    {
        $success = 1;
        $password = substr(hash('sha512', rand()), 0, 8);
        $isEmailVerificationEnabled = get_setting('email_verification');

        // User Create
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = addon_is_activated('otp_system') ? $request->phone : null;
        $user->password = Hash::make($password);
        $user->email_verified_at = $isEmailVerificationEnabled != 1 ? date('Y-m-d H:m:s') : null;
        $user->save();

        // Account Opening and verification(if activated) eamil send
        try {
            EmailUtility::customer_registration_email('registration_from_system_email_to_customer', $user, $password);
        } catch (\Exception $e) {
            $success = 0;
            $user->delete();
        }

        if($success == 0){
            return response()->json([
                'result' => false,
                'message' => translate('Something went wrong!')
            ]);
        }

        if($isEmailVerificationEnabled == 1){
            $user->notify(new AppEmailVerificationNotification());
        }

        // User Address Create
        $address = new Address();
        $address->user_id       = $user->id;
        $address->address       = $request->address;
        $address->country_id    = $request->country_id;
        $address->state_id      = $request->state_id;
        $address->city_id       = $request->city_id;
        $address->postal_code   = $request->postal_code;
        $address->phone         = $request->phone;
        $address->longitude     = $request->longitude;
        $address->latitude      = $request->latitude;
        $address->save();

        Cart::where('temp_user_id', $request->temp_user_id)
            ->update([
                'user_id' => $user->id,
                'temp_user_id' => null,
                'address_id' => $address->id
            ]);

        //create token
        $user->createToken('tokens')->plainTextToken;

        return $this->loginSuccess($user);
    }

      public function loginSuccess($user, $token = null, $tempUserId = null)
    {
        if (!$token) {
            $token = $user->createToken('API Token')->plainTextToken;
        }

        if($tempUserId != null){
            Cart::where('temp_user_id', $tempUserId)
                ->update([
                    'user_id' => $user->id,
                    'temp_user_id' => null
                ]);
        }

        Auth::login($user);
        session()->flash('redirect_to', route('dashboard')); 

        if($user->user_type == 'seller'){
            \Log::channel('seller_login')->info('Seller Logged In', [
                'user_id' => $user->id,
                'email' => $user->email,
                'time' => now()->toDateTimeString()
            ]);
        }

        return response()->json([
            'result' => true,
            'message' => translate('Successfully logged in'),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => null,
            'user' => [
                'id' => $user->id,
                'type' => $user->user_type,
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'avatar_original' => uploaded_asset($user->avatar->avatar_original), 
                'phone' => $user->phone,
                'email_verified' => $user->email_verified_at != null
            ]
        ]);
    }


    protected function loginFailed()
    {

        return response()->json([
            'result' => false,
            'message' => translate('Login Failed'),
            'access_token' => '',
            'token_type' => '',
            'expires_at' => null,
            'user' => [
                'id' => 0,
                'type' => '',
                'name' => '',
                'email' => '',
                'avatar' => '',
                'avatar_original' => '',
                'phone' => ''
            ]
        ]);
    }


    public function account_deletion()
    {
        if (auth()->user()) {
            Cart::where('user_id', auth()->user()->id)->delete();
        }
        $auth_user = auth()->user();
        $auth_user->tokens()->where('id', $auth_user->currentAccessToken()->id)->delete();
        $auth_user->customer_products()->delete();

        User::destroy(auth()->user()->id);

        return response()->json([
            "result" => true,
            "message" => translate('Your account deletion successfully done')
        ]);
    }

    public function getUserInfoByAccessToken(Request $request)
    {
        $token = PersonalAccessToken::findToken($request->access_token);
        if (!$token) {
            return $this->loginFailed();
        }
        $user = $token->tokenable;

        if ($user == null) {
            return $this->loginFailed();
        }

        return $this->loginSuccess($user, $request->access_token);
    }
}
