<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerificationCodeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Resend OTP verification code
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendVerificationCode(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $userEmail = $request->email;

            // Generate new 6 digit random code
            $code = rand(100000, 999999);

            // Update verification code in database
            \DB::table('verification_codes')->updateOrInsert(
                ['email' => $userEmail],
                [
                    'code' => $code,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Send verification email
            Mail::to($userEmail)->send(new VerificationCodeMail($code));

            return response()->json([
                'result' => true,
                'message' => 'Verification code resent successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => 'Failed to resend verification code. Please try again.'
            ], 500);
        }
    }
    public function verify($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            return redirect('/')->with('error', 'Invalid or expired token.');
        }

        $user->is_verified = true;
        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();

        auth()->login($user);

        return redirect('/')->with('success', 'Email verified successfully! You are now logged in.');
    }

    /**
     * Send OTP verification code to user email
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerificationCode(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $userEmail = $request->email;

            // Generate 6 digit random code
            $code = rand(100000, 999999);

            // Store verification code in database with expiration
            \DB::table('verification_codes')->updateOrInsert(
                ['email' => $userEmail],
                [
                    'code' => $code,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Send verification email
            try {
                Mail::to($userEmail)->send(new VerificationCodeMail($code));
                \Log::info('Verification email sent successfully to: ' . $userEmail);
            } catch (\Exception $mailException) {
                \Log::error('Mail sending failed: ' . $mailException->getMessage());
                return response()->json([
                    'result' => false,
                    'message' => 'Failed to send email: ' . $mailException->getMessage()
                ], 500);
            }

            return response()->json([
                'result' => true,
                'message' => 'Verification code sent successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Verification code sending failed: ' . $e->getMessage());
            return response()->json([
                'result' => false,
                'message' => 'Failed to send verification code: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Verify OTP code and login user
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verification_confirmation(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'code' => 'required|string|size:6'
            ]);

            $email = $request->email;
            $code = $request->code;

            // Check if code exists and is not expired (30 minutes)
            $record = \DB::table('verification_codes')
                ->where('email', $email)
                ->where('code', $code)
                ->first();

            if (!$record) {
                return response()->json([
                    'result' => false,
                    'message' => 'Invalid or expired verification code.'
                ], 400);
            }
            
            // Check if code is expired (created more than 30 minutes ago)
            $created_at = Carbon::parse($record->created_at);
            if ($created_at->diffInMinutes(now()) > 30) {
                return response()->json([
                    'result' => false,
                    'message' => 'Verification code has expired. Please request a new one.'
                ], 400);
            }

            // Find or create user
            $user = User::where('email', $email)->first();
            if (!$user) {
                // Create new user if doesn't exist - email-only authentication
                $user = User::create([
                    'name' => 'Customer', // Default name for email-only users
                    'email' => $email,
                    'user_type' => 'customer',
                    'email_verified_at' => now(),
                    'is_verified' => true,
                    'password' => bcrypt(\Illuminate\Support\Str::random(8)) // Random password
                ]);
            } else {
                // Update existing user
                $user->email_verified_at = now();
                $user->save();
            }

            // Delete used verification code
            \DB::table('verification_codes')->where('email', $email)->delete();

            // Log user into Laravel session for proper authentication
            auth()->login($user, true);

            // Generate access token for API usage
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'result' => true,
                'message' => 'Email verified successfully!',
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name ?: 'Customer', // Default name if empty
                    'email' => $user->email,
                    'user_type' => $user->user_type
                ]
            ]);
        }catch (\Exception $e) {
    \Log::error('Verification Exception: ' . $e->getMessage(), [
        'trace' => $e->getTraceAsString(),
        'request' => $request->all()
    ]);

    return response()->json([
        'result' => false,
        'message' => 'Verification failed. Please check the logs.'
    ], 500);
}
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'result' => false,
        //         'message' => 'Verification failed. Please try again.'
        //     ], 500);
        // }
    }




    // public function show(Request $request)
    // {
    //     if ($request->user()->email != null) {
    //         return $request->user()->hasVerifiedEmail()
    //                         ? redirect($this->redirectPath())
    //                         : view('auth.'.get_setting('authentication_layout_select').'.verify_email');
    //     }
    //     else {
    //         $otpController = new OTPVerificationController;
    //         $otpController->send_code($request->user());
    //         return redirect()->route('verification');
    //     }
    // }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }

    // public function verification_confirmation($code){
    //     $user = User::where('verification_code', $code)->first();
    //     if($user != null){
    //         $user->email_verified_at = Carbon::now();
    //         $user->save();
    //         auth()->login($user, true);
    //         offerUserWelcomeCoupon();
    //         flash(translate('Your email has been verified successfully'))->success();
    //     }
    //     else {
    //         flash(translate('Sorry, we could not verifiy you. Please try again'))->error();
    //     }

    //     if($user->user_type == 'seller') {
    //         return redirect()->route('seller.dashboard');
    //     }

    //     return redirect()->route('dashboard');
    // }
}
