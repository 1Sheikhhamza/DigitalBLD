<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'subscriber/subscription/package/success',
            'subscriber/subscription/package/cancel',
            'subscriber/subscription/package/fail',
        ]);
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            //'auth.api' => \App\Http\Middleware\Authenticate::class,         // API authentication using Passport
            'auth.api' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
            'auth.admin' => \App\Http\Middleware\AdminAuthenticate::class,  // Admin authentication
            'auth.user' => \App\Http\Middleware\UserAuthenticate::class,    // User authentication
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.permission' => \App\Http\Middleware\CheckPermission::class,
            'has.subscription' => \App\Http\Middleware\EnsureSubscriberHasActiveSubscription::class,
            'check.subscriber.permission' => \App\Http\Middleware\CheckSubscriberPermission::class,
            'single.device.subscriber' => \App\Http\Middleware\EnsureSingleDeviceSubscriber::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
