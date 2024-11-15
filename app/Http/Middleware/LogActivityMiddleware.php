<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Auth;
use App\ActivityLog;

use Closure;

class LogActivityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if($user)
        {
            $logData = [
                'user_id' => $user->id,
                'username' => $user->username,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'agent' => $request->header('User-Agent'),
                'time' => now(),
            ];

            ActivityLog::create([
                'user_id' => $user->id,
                'username' => $user->username,
                'log_data' => json_encode($logData),
                'time' => now(), // Simpan log sebagai JSON
            ]);
        }

        return $next($request);
    }
}
