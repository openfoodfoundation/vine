<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PreventTooManyRequestsMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request                      $request
     * @param Closure(Request): (Response) $next
     *
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip        = $request->ip();
        $userAgent = $request->header('User-Agent');

        /**
         * Create an identifier based on the IP address and the User-Agent header value
         * (as the user might be part of a shared IP network)
         */
        $identifier = md5($ip . $userAgent);

        /**
         * Check if there was a cache hit for the identifier and whether a request
         * has been made from this identifier within the last minute
         */
        $recentRequest = Cache::get($identifier);

        if ($recentRequest && $recentRequest > now()->subMinute()) {
            return response()->json([
                'message' => 'Too many requests. Please wait before trying again.',
            ], 429);
        }

        /**
         * Store the identifier and the current time in the cache
         * with a lifetime of 60 seconds
         */
        Cache::put($identifier, now(), 60);

        return $next($request);
    }
}
