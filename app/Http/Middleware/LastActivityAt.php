<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Auth\LoginController;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class LastActivityAt
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
        if (Auth::check()) {
            $user = Auth::user();

            if ($user->role_id == 3) {
                $time = Settings('device_limit_time');
                $last_activity = $user->last_activity_at;
                if ($time != 0) {
                    if (!empty($last_activity)) {
                        $valid_activity = Carbon::parse($last_activity)->addMinutes($time);
                        $current_time = Carbon::now();
                        if ($current_time->lt($valid_activity)) {
//                            Toastr::success('in');
                        } else {
//                            Toastr::error('out');
                            $loginController = new LoginController();
                            $loginController->logout($request);

                        }
                    }
                }

                $user->last_activity_at = now();
                $user->save();
            }

        }
        return $next($request);
    }
}
