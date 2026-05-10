<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(function ($request) {
            return $request->user()->isAdmin()
                ? route('admin.dashboard')
                : route('account.profile');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('admin/*')) {
                return redirect()->guest(route('admin.login'));
            }

            return redirect()->guest(route('login'));
        });
    })->create();
