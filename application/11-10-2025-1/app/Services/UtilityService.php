<?php

namespace App\Services;

use App\Mail\OtpMail;
use App\Models\Otp;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class UtilityService
{

    // Mail::to($email)->queue(new OtpMail($otp));
    // Mail::to($email)->later(now()->addSeconds(5), new OtpMail($otp));

    public static function generateAndSendOtp(string $email, string $ip, string $type)
    {
        $otp = rand(100000, 999999);
        Otp::where('email', $email)->update(['status' => 0]);

        if ($type === 'registration') {
            Otp::updateOrCreate(
                [
                    'email' => $email,
                    'type'  => $type
                ],
                [
                    'otp'         => $otp,
                    'ipaddress'   => $ip,
                    'expires_at'  => now()->addMinutes(5),
                    'status'      => 1,
                    'is_verified' => 0,
                    'attempts'    => 0,
                ]
            );


            self::storeTime();
            try {
                Mail::to($email)->send(new OtpMail($otp)); // Or queue() if using sync queue
            } catch (\Exception $e) {
                Log::error("Failed to send OTP mail to $email: " . $e->getMessage());
            }
        } else if ($type === 'forgot-password') {
            Otp::updateOrCreate(
                [
                    'email' => $email,
                    'type'  => $type
                ],
                [
                    'otp'         => $otp,
                    'ipaddress'   => $ip,
                    'expires_at'  => now()->addMinutes(5),
                    'status'      => 1,
                    'is_verified' => 0,
                    'attempts'    => 0,
                ]
            );


            self::storeTime();
            try {
                Mail::to($email)->send(new OtpMail($otp));
            } catch (\Exception $e) {
                Log::error("Failed to send OTP mail to $email: " . $e->getMessage());
            }
        }
    }


    private static function storeTime()
    {
        if (!session()->has('otp_expires_at') || now()->gte(session('otp_expires_at'))) {
            session(['otp_expires_at' => now()->addMinutes(5)]);
        }
    }


    public static function resend($request)
    {
        if (!session('subscriber_register')) {
            return redirect()->route($request['redirect'])->withErrors(['otp' => 'Unauthorized access']);
        }

        self::generateAndSendOtp(session('subscriber_register')['email'], request()->ip(), $request['type']);
        return redirect()->route($request['redirect'])->with('success', 'OTP resent successfully');
    }
}
