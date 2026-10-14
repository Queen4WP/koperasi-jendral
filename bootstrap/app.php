<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin*')) {
                return route('admin.login');
            }
            if ($request->is('finance*')) {
                return route('finance.login');
            }
            if ($request->is('kasir*')) {
                return route('kasir.login');
            }
            if ($request->is('karyawan*')) {
                return route('karyawan.login');
            }
            return route('login');
        });
        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            if ($request->is('admin*')) {
                return route('admin.dashboard');
            }
            if ($request->is('finance*')) {
                return route('finance.dashboard');
            }
            if ($request->is('kasir*')) {
                return route('kasir.dashboard');
            }
            if ($request->is('karyawan*')) {
                return route('karyawan.dashboard');
            }
            return route('member.dashboard');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
