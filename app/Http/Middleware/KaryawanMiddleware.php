<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KaryawanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('karyawan')->user() ?? auth('web')->user();

        if (!$user || $user->role !== 'karyawan') {
            if ($request->is('karyawan*')) {
                return redirect()->route('karyawan.login');
            }
            abort(403, 'Unauthorized action. Karyawan role required.');
        }

        return $next($request);
    }
}
