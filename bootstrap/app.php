<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// ... import lain kalau perlu ...

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            // Kalau nanti butuh alias lain, tambah di sini
            // 'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // Kalau kamu pakai Spatie Permission juga (dari error sebelumnya), tambah ini sekalian:
        // 'role'               => \Spatie\Permission\Middleware\RoleMiddleware::class,
        // 'permission'         => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        // 'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
