<?php

namespace App\Http\Middleware;

use App\Models\UserDeviceLog;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EnsureSingleDeviceSubscriber
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('subscriber')->check()) {
            $subscriber = Auth::guard('subscriber')->user();
            $currentSessionId = session()->getId();

            $currentDevice = UserDeviceLog::where('subscriber_id', $subscriber->id)
                ->where('session_id', $currentSessionId)
                ->where('is_current', true)
                ->first();

            if (!$currentDevice) {
                Auth::guard('subscriber')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('subscriber.login')->withErrors([
                    'message' => 'You have been logged out because your account was accessed from another device.'
                ]);
            }
        }

        return $next($request);
    }
}
