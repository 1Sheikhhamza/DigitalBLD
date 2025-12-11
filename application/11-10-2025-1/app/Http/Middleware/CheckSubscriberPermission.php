<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSubscriberPermission
{
    public function handle(Request $request, Closure $next)
    {
        $subscriber = auth('subscriber')->user();
        if (!$subscriber) {
            abort(403, 'Unauthorized');
        }

        $routeName = $request->route()->getName();
        if (!$routeName) {
            abort(403, 'Unauthorized');
        }

        // ✅ Allow if subscriber has the module or related feature access
        if (
            !$subscriber->hasModule($routeName) &&
            !$subscriber->hasAnyPermissionOnResource($routeName)
        ) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
