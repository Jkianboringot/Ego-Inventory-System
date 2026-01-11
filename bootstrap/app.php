<?php

use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\PermissionMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'permission' => CheckPermission::class  // Use the new optimized middleware
                            //what controll where what happen when somthing has no permissin
                            //check CheckPermission
        ]);
          $middleware->trustProxies(at: '*');
    })
    ->withProviders([
        // ADD THIS LINE - Register your Permission Service Provider
        \App\Providers\PermissionServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();