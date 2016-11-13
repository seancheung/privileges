<?php

namespace Panoscape\Privileges\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class PrivilegesMiddleware
{
    protected $auth;

    /**
     * Creates a new instance of the middleware.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

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
        if ($this->auth->guest() || !$request->user()->validate($pattern, $column)) {
            abort(403);
        }
        return $next($request);
    }
}
