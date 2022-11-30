<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ( config('app.db_database') == '' ) {
            //return redirect()->route('install.index');
        }
        if (Auth::guard($guard)->check()) {
            return redirect('/admin/dashboard');
        }

        return $next($request);
    }
}
