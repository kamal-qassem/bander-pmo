<?php

namespace App\Http\Middleware;

use Closure;

class Plugin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $slug)
    {
        if ( config('app.db_database') != '' ) {
            if ( config('app.db_database') != '' && ! isPluginActive($slug) ) {
                flashMessage('danger', 'create', 'not-allowed');
                return redirect('admin/dashboard');
            }
        }
        return $next($request);
    }
}
