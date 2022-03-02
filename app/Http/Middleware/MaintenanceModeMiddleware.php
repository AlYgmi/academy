<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;


class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Settings('maintenance_status') == 1) {
            $maintain = DB::table('home_contents')
                ->select('maintenance_status',
                    'maintenance_title', 'maintenance_sub_title', 'maintenance_banner'
                )->first();

            return new response(view(theme('pages.maintenance'), compact('maintain')));
        }


        return $next($request);
    }
}
