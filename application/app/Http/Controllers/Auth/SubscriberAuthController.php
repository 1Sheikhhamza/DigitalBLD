<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\BaseController;
use App\Mail\OtpMail;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscriber;
use App\Models\UserDeviceLog;
use App\Services\UtilityService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Services\HomeService;
use Illuminate\Support\Facades\DB;

class SubscriberAuthController extends BaseController
{
    public function __construct(HomeService $homeService)
    {
        parent::__construct($homeService);
    }

    public function showLoginForm()
    {
        return view('auth.subscribers.login');
    }

    /* public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (auth()->guard('subscriber')->attempt($credentials)) {
            if ($request->back) {
                return redirect()->to($request->get('back'));
            }

            return redirect()->route('subscriber.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    } */


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('subscriber')->attempt($credentials)) {
            $subscriber = Auth::guard('subscriber')->user();
            session()->regenerate();
            $currentSessionId = session()->getId();

            // 1️⃣ Remove other sessions (force logout previous devices)
            DB::table('sessions')
                ->where('subscriber_id', $subscriber->id)
                ->where('id', '!=', $currentSessionId)
                ->delete();

            // 2️⃣ Mark old device logs as not current
            UserDeviceLog::where('subscriber_id', $subscriber->id)
                ->update(['is_current' => false, 'logged_out_at' => now()]);

            // 3️⃣ Create new device log for current session
            UserDeviceLog::create([
                'subscriber_id' => $subscriber->id,
                'guard'         => 'subscriber',
                'session_id'    => $currentSessionId,
                'ip_address'    => $request->ip(),
                'user_agent'    => $request->userAgent(),
                'device_name'   => $this->parseDeviceName($request->userAgent()),
                'browser'       => $this->parseBrowser($request->userAgent()),
                'platform'      => $this->parsePlatform($request->userAgent()),
                'is_current'    => true,
                'logged_in_at'  => now(),
            ]);

            return $request->back
                ? redirect()->to($request->get('back'))
                : redirect()->route('subscriber.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }



    protected function parseDeviceName($ua)
    {
        if (stripos($ua, 'Mobile') !== false) return 'Mobile';
        if (stripos($ua, 'Tablet') !== false) return 'Tablet';
        return 'Desktop';
    }
    protected function parseBrowser($ua)
    {
        if (stripos($ua, 'Firefox') !== false) return 'Firefox';
        if (stripos($ua, 'Chrome') !== false) return 'Chrome';
        if (stripos($ua, 'Safari') !== false) return 'Safari';
        return 'Unknown';
    }
    protected function parsePlatform($ua)
    {
        if (stripos($ua, 'Windows') !== false) return 'Windows';
        if (stripos($ua, 'Mac') !== false) return 'macOS';
        if (stripos($ua, 'Linux') !== false) return 'Linux';
        if (stripos($ua, 'Android') !== false) return 'Android';
        if (stripos($ua, 'iPhone') !== false || stripos($ua, 'iPad') !== false) return 'iOS';
        return 'Unknown';
    }

    public function showRegisterForm()
    {
        return view('auth.subscribers.register');
    }

    /* public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required|email|unique:subscribers,email',
            'password' => 'required|confirmed',
        ]);

        Subscriber::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('subscriber.login')->with('success', 'Registered successfully');
    } */

    public function sendOtp(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'dob' => 'nullable|date|before:-18 years',
            'registration_as' => 'required|in:Judiciary Person,Lawyer,Student,Other', // adjust values as needed
            'mobile' => [
                'required',
                'digits:11',
                'regex:/^[0-9]+$/',
                'unique:subscribers,mobile',
            ],
            'email' => 'required|email|unique:subscribers,email',
        ]);



        $this->generateAndSendOtp($request->email, $request->ip(), 'registration');
        // session(['email' => $request->email]);
        session([
            'subscriber_register' => $request->only([
                'first_name',
                'last_name',
                'dob',
                'registration_as',
                'mobile',
                'email'
            ])
        ]);

        return redirect()->route('subscriber.otp')->with('success', 'OTP sent to your email');
    }

    /* public function resend(Request $request)
    {
        if (!session('subscriber_register')) {
            return redirect()->route('subscriber.register')->withErrors(['otp' => 'Unauthorized access']);
        }

        $this->generateAndSendOtp(session('subscriber_register')['email'], $request->ip(), 'registration');
        return redirect()->route('subscriber.otp')->with('success', 'OTP resent successfully');
    } */

    public function resend(Request $request)
    {
        return UtilityService::resend($request->except('_token'));
    }

    private function generateAndSendOtp(string $email, string $ip, string $type)
    {
        $otp = rand(100000, 999999);

        // Deactivate previous OTPs
        Otp::where('email', $email)->update(['status' => 0]);

        // Insert or update OTP
        if ($type === 'registration') {
            Otp::updateOrCreate(
                ['email' => $email, 'type' => $type],
                [
                    'otp' => $otp,
                    'ipaddress' => $ip,
                    'expires_at' => now()->addMinutes(5),
                    'status' => 1,
                    'is_verified' => 0,
                    'attempts' => 0,
                ]
            );

            // Calculate remaining seconds
            $this->storeTime();

            // Queue custom Mailable
            // Mail::to($email)->queue(new OtpMail($otp));
            // Mail::to($email)->later(now()->addSeconds(5), new OtpMail($otp));
            // Mail::to($email)->sendNow(new OtpMail($otp));
            /* Mail::raw("Your OTP is: $otp", function ($message) use ($email) {
                $message->to($email)->subject('Your OTP Code');
            }); */
            try {
                Mail::to($email)->send(new OtpMail($otp)); // Or queue() if using sync queue
            } catch (\Exception $e) {
                Log::error("Failed to send OTP mail to $email: " . $e->getMessage());
                // Optional: trigger retry logic or notify admin
            }
        } else {
            // For resend: insert new row
            Otp::insert([
                'email' => $email,
                'ipaddress' => $ip,
                'type' => $type,
                'otp' => $otp,
                'expires_at' => now()->addMinutes(5),
                'status' => 1,
                'is_verified' => 0,
                'attempts' => 0,
                'created_at' => now(),
            ]);

            $this->storeTime();
            // Raw mail for resend (or you could reuse OtpMail)
            // Mail::to($email)->queue(new OtpMail($otp));
            // Mail::to($email)->later(now()->addSeconds(5), new OtpMail($otp));
            try {
                Mail::to($email)->send(new OtpMail($otp)); // Or queue() if using sync queue
            } catch (\Exception $e) {
                Log::error("Failed to send OTP mail to $email: " . $e->getMessage());
                // Optional: trigger retry logic or notify admin
            }
        }
    }


    private function storeTime()
    {
        if (!session()->has('otp_expires_at') || now()->gte(session('otp_expires_at'))) {
            session(['otp_expires_at' => now()->addMinutes(5)]);
        }
    }


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
            ->where('type', 'registration')
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

        session(['otp_verified' => true]);

        return redirect()->route('subscriber.setPassword');
    }

    public function completeRegistration(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[!@#$%^&*(),.?":{}|<>]/',
                'confirmed'
            ]
        ], [
            'password.regex' => 'Password must include at least one lowercase letter, one uppercase letter, one number, and one special character.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);



        if (!session('otp_verified')) {
            return redirect()->route('subscriber.register')->withErrors(['otp' => 'Unauthorized access']);
        }

        $data = session('subscriber_register');

        $dob = null;
        if (!empty($data['dob'])) {
            try {
                // Try common formats
                $dob = Carbon::createFromFormat('d/m/Y', $data['dob']);
            } catch (\Exception $e1) {
                try {
                    $dob = Carbon::createFromFormat('d-m-Y', $data['dob']);
                } catch (\Exception $e2) {
                    try {
                        $dob = Carbon::parse($data['dob']); // fallback for Y-m-d or other valid formats
                    } catch (\Exception $e3) {
                        Log::warning('Invalid DOB format entered: ' . $data['dob']);
                        $dob = null; // or you can set a default date if you want
                    }
                }
            }
        }

        Subscriber::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'mobile' => $data['mobile'],
            'email' => $data['email'],
            'dob' => $dob,
            'registration_as' => $data['registration_as'],
            'password' => Hash::make($request->password),
        ]);


        return redirect()->route('subscriber.success')->with('success', 'Registered successfully!');
    }

    public function showOtpForm()
    {
        if (!session('subscriber_register')) {
            return redirect()->route('subscriber.register')->withErrors(['otp' => 'Unauthorized access']);
        }



        // Calculate remaining seconds
        $expiresAt = session('otp_expires_at');
        $remainingSeconds = round(now()->diffInSeconds($expiresAt));
        // $otpEmail = session("subscriber_register")['email'];
        // $remainingTime = session('otp_time');
        return view('auth.subscribers.otp', compact('remainingSeconds'));
    }

    public function showPasswordForm()
    {
        if (!session('otp_verified')) {
            return redirect()->route('subscriber.register')->withErrors(['otp' => 'Unauthorized access']);
        }
        return view('auth.subscribers.set-password');
    }

    public function success()
    {
        /* if (!session('otp_verified')) {
            return redirect()->route('subscriber.register')->withErrors(['otp' => 'Unauthorized access']);
        }
        session()->forget(['subscriber_register', 'otp_verified']); */
        return view('auth.subscribers.success');
    }

    /* public function logout()
    {
        Auth::guard('subscriber')->logout();
        return redirect()->route('subscriber.login');
    } */

    public function logout(Request $request)
    {
        $guard = Auth::guard('subscriber');
        $subscriber = $guard->user();

        if ($subscriber) {
            $currentSessionId = session()->getId();

            UserDeviceLog::where('subscriber_id', $subscriber->id)
                ->where('session_id', $currentSessionId)
                ->update(['is_current' => false, 'logged_out_at' => now()]);

            DB::table('sessions')->where('id', $currentSessionId)->delete();
        }

        $guard->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('subscriber.login');
    }
}
