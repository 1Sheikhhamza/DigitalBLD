<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        if (empty($guards)) {
            $guards = ['web']; // Default guard
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                switch ($guard) {
                    case 'administration':
                        return redirect()->route('admin.dashboard');
                    case 'subscriber':
                        return redirect()->route('subscriber.dashboard');
                }
            }
        }

        // If not logged in, allow request to continue (e.g. show login page)
        return $next($request);
    }
}
