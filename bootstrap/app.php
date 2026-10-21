<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\{Exceptions, Middleware};

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        \App\Console\Commands\MonthlySummaryCommand::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        // ✅ បន្ថែម TrackReferral ទៅក្នុង Web Middleware Group ដើម្បីចាំស្កែនរកកូដលីងណែនាំ
        $middleware->web(append: [
            \App\Http\Middleware\TrackReferral::class,
        ]);

        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'admin'      => \App\Http\Middleware\AdminMiddleware::class,
            'api.key'    => \App\Http\Middleware\ApiKeyMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();