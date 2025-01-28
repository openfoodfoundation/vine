<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class CheckIfPasswordUpdateRequired
{
    protected array $except = [
        'profile.set-password',
    ];

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
        if (Auth::user() && Auth::user()->requires_password_reset == 1 && ($request->route()->uri != 'profile/set-password')) {

            return Redirect::to('/profile/set-password');
        }

        return $next($request);

    }
}
