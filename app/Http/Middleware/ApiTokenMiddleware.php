<?php

namespace App\Http\Middleware;

use App\ApiToken;

use Closure;

class ApiTokenMiddleware
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
        $token = $request->bearerToken();

        if (!$token || !ApiToken::where('token', $token)->exists()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // ApiToken::where('token', '=', $token)->update([
        //     'last_used_at' => now()
        // ]);

        return $next($request);
    }
}
