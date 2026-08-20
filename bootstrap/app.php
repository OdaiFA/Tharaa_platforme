<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')->group(__DIR__.'/../routes/admin.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);

        $middleware->throttleApi('60,1');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Business-rule violations thrown throughout the service layer
        // (insufficient balance, currency mismatch, exhausted quiz
        // attempts, etc.) are usually caught locally where they're thrown
        // and turned into a friendly inline error. This is the global
        // fallback for any path that doesn't — without it, an uncaught
        // \DomainException or \InvalidArgumentException renders as a raw
        // 500 / stack trace instead of a message the user can act on.
        $render = function (\Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        };

        $exceptions->renderable(fn (\DomainException $e, $request) => $render($e, $request));
        $exceptions->renderable(fn (\InvalidArgumentException $e, $request) => $render($e, $request));
    })->create();
