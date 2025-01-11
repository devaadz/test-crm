<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, )
    {

        if (auth()->user() && auth()->user()->role === 'super_admin' || auth()->user() && auth()->user()->role === 'manager' ) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
