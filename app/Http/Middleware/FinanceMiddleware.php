<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinanceMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('finance')->user() ?? auth('web')->user();

        if (!$user || $user->role !== 'finance') {
            if ($request->is('finance*')) {
                return redirect()->route('finance.login');
            }
            abort(403, 'Unauthorized action. Finance role required.');
        }

        return $next($request);
    }
}
