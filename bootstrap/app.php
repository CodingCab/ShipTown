<?php

use Aws\Laravel\AwsServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Milon\Barcode\BarcodeServiceProvider;
use Sentry\Laravel\ServiceProvider as SentryServiceProvider;
use Barryvdh\DomPDF\ServiceProvider as DomPDFServiceProvider;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        AwsServiceProvider::class,
        SentryServiceProvider::class,
        DomPDFServiceProvider::class,
        BarcodeServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo('/dashboard');

        $middleware->append(\App\Http\Middleware\AddHeaderAccessToken::class);

        $middleware->web([
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Laravel\Passport\Http\Middleware\CreateFreshApiToken::class,
        ]);

        $middleware->throttleApi('240,1');

        $middleware->alias([
            'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
            'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
            'twofactor' => \App\Http\Middleware\TwoFactor::class,
        ]);

        $middleware->priority([
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\Authenticate::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Auth\Middleware\Authorize::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
