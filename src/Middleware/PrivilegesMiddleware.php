<?php

namespace Panoscape\Privileges\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class PrivilegesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param  string $pattern
     * @param  string $column
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $pattern, $column = 'name')
    {
        if (auth()->guest() || !$request->user()->validate($pattern, $column)) {
            abort(403);
        }
        return $next($request);
    }
}
