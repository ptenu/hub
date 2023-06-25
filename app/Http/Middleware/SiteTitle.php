<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SiteTitle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $siteName = env("PUBLIC_SITE_NAME", env('APP_NAME', 'Peterborough Tenants Union'));

        if (str_starts_with($request->host(), 'app.')) {
            $siteName = 'PTU App';
        }

        View::share('siteName', $siteName);

        return $next($request);
    }
}
