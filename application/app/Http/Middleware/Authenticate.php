<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        // Default guards if none specified
        if (empty($guards)) {
            $guards = ['web', 'administration', 'api', 'subscriber'];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }

        // Not authenticated: get the full current URL to redirect back after login
        $intendedUrl = $request->fullUrl();

        // Redirect based on route prefix or guard logic
        if ($request->is('admin/*') || $request->is('admin')) {
            // Admin login route with back param
            return redirect()->route('admin.login', ['back' => $intendedUrl]);
        } elseif ($request->is('api/*')) {
            // API request unauthorized JSON response
            return response()->json(['error' => 'Unauthorized'], 401);
        } elseif ($request->is('subscriber/*') || in_array('subscriber', $guards)) {
            // Investor login route with back param
            return redirect()->route('subscriber.login', ['back' => $intendedUrl]);
        } else {
            // Default user login with back param
            return redirect()->route('login', ['back' => $intendedUrl]);
        }
    }
}
