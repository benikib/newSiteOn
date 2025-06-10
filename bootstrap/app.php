<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsSubscribed;
use App\Http\Middleware\EtablissementMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

            $middleware->alias([
        'admins' => AdminMiddleware::class,
        'etablissements' => EtablissementMiddleware::class,
    ]);
    })




    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
