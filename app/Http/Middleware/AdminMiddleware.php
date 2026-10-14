<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('admin')->user() ?? auth('web')->user();

        if (!$user || $user->role !== 'admin') {
            if ($request->is('admin*')) {
                return redirect()->route('admin.login');
            }
            abort(403, 'Unauthorized action. Admin role required.');
        }

        return $next($request);
    }
}
