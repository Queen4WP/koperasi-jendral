<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class KasirMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('kasir')->user() ?? auth('web')->user();

        if (!$user || $user->role !== 'kasir') {
            if ($request->is('kasir*')) {
                return redirect()->route('kasir.login');
            }
            abort(403, 'Unauthorized action. Kasir role required.');
        }

        return $next($request);
    }
}
