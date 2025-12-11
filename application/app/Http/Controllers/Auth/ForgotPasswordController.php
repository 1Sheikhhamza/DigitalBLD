<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Frontend\BaseController;
use App\Models\Subscriber;
use App\Models\Otp;
use App\Services\UtilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Services\HomeService;

class ForgotPasswordController extends BaseController
{

    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
    }
    // Show form to request OTP
    public function showOtpRequestForm()
    {
        return view('auth.subscribers.forgot_password.email');
    }

    // Send OTP
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:subscribers,email']);

        $checkUserExist = Subscriber::select('id', 'name', 'email')->where('email', $request->email)->first();
        if (!$checkUserExist) {
            return redirect()->back()->withErrors(['email' => 'User not found']);
        }

        UtilityService::generateAndSendOtp($request->email, $request->ip(), 'forgot-password');
        session([
            'subscriber_register' => $request->only([
                'email'
            ])
        ]);

        return redirect()->route('subscriber.otp.verify')->with('email', $request->email);
    }

    // Show form to verify OTP
    public function showOtpVerificationForm(Request $request)
    {
        $expiresAt = session('otp_expires_at');
        $remainingSeconds = round(now()->diffInSeconds($expiresAt));
        return view('auth.subscribers.forgot_password.otp', compact('remainingSeconds'));
    }

    // Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $temp = session('subscriber_register');

        if (!$temp) {
            return back()->withErrors(['email' => 'Session expired. Please start over.']);
        }

        $otpRecord = Otp::where('email', $temp['email'])
            ->where('type', 'forgot-password')
            ->where('status', 1)
            ->first();

        if (!$otpRecord || $otpRecord->otp != $request->otp || now()->gt($otpRecord->expires_at)) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP']);
        }

        // Mark OTP as verified
        $otpRecord->update([
            'is_verified' => 1,
            'status' => 0
        ]);

        session(['otp_verified' => true,'forgotPasswordTimestamp' => now()->addMinutes(5)]);

        return redirect()->route('subscriber.password.reset');
    }

    public function showResetPasswordForm(Request $request)
    {
        $forgotPasswordEmail = session('subscriber_register')['email'];
        // $forgotPasswordTimestamp = session('forgotPasswordTimestamp');

        /* if (!$forgotPasswordEmail || !$forgotPasswordTimestamp) {
            return redirect()->route('subscriber.password.request')->withErrors(['email' => 'Session expired. Please request a new password reset.']);
        } */

        if (!$forgotPasswordEmail) {
            return redirect()->route('subscriber.password.request')->withErrors(['email' => 'Session expired. Please request a new password reset.']);
        }

        return view('auth.subscribers.forgot_password.reset', ['email' => $request->session()->get('email')]);
    }

    // Reset password
    public function resetPassword(Request $request)
    {
        $forgotPasswordEmail = session('subscriber_register')['email'];
        // $forgotPasswordTimestamp = session('forgotPasswordTimestamp');

        if (!$forgotPasswordEmail) {
            return redirect()->route('subscriber.password.forgot')->withErrors(['email' => 'Session expired. Please request a new password reset.']);
        }

        $request->validate([
            'password' => 'required|confirmed|min:6',
        ]);

        $user = Subscriber::where('email', $forgotPasswordEmail)->first();
        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'Email not found']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $request->session()->forget('subscriber_register');
        // $request->session()->forget('forgotPasswordTimestamp');
        return redirect('subscriber/login')->with('status', 'Password reset successfully');
    }
}
