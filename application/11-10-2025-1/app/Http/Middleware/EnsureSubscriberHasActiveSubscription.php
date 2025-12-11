<?php

namespace App\Http\Middleware;

use App\Models\Subscriber;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureSubscriberHasActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $subscriber = Auth::guard('subscriber')->user();

        if (!$subscriber) {
            return redirect()->route('login')
                ->with('error', 'Please login first.');
        }

        // Lazy eager load activeSubscription relation if not already loaded
        if (! $subscriber->relationLoaded('activeSubscription')) {
            $subscriber->load('activeSubscription');
        }

        if (!$subscriber->activeSubscription) {
            return redirect()->route('package')
                ->with('error', 'You must have an active subscription to access this page.');
        }

        return $next($request);
    }
}
