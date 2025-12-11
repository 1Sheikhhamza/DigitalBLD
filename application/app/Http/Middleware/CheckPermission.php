<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('administration')->user();
        // dd($user->getAllPermissions()->pluck('name'));

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $routeName = $request->route()->getName();

        if (!$routeName) {
            abort(403, 'Unauthorized');
        }

        // Map controller action names to permission actions
        $map = [
            'index'   => 'list',
            'show'    => 'view',
            'create'  => 'create',
            'store'   => 'create',
            'edit'    => 'edit',
            'update'  => 'edit',
            'destroy' => 'delete',
        ];

        $permission = null;

        // Match route names like 'hospital.create', 'user.edit', etc.
        if (preg_match('/^(\w+)\.(\w+)$/', $routeName, $matches)) {
            $resource = $matches[1]; // e.g. hospital
            $action = $matches[2];   // e.g. create

            // Use mapped action or fallback to original action
            $action = $map[$action] ?? $action;

            // Compose permission in best practice format: resource.action
            $permission = $resource . '.' . $action;
        } else {
            // Fallback: replace dots with underscore or space (depends on your convention)
            // Here we fallback to the original route name as permission
            $permission = $routeName;
        }

        // Check permission using dot notation like 'hospital.create'
        if (!$user->can($permission)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
