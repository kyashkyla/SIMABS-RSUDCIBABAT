<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Tambahkan ini
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        // Tamu yang mengakses /admin/* diarahkan ke halaman login admin,
        // bukan ke halaman login pegawai.
        $middleware->redirectGuestsTo(function ($request) {
            return $request->is('admin/*')
                ? route('admin.login')
                : route('login');
        });

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();