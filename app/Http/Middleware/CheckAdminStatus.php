<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace App\Http\Middleware;

use App\Enums\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminStatus
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
        if (Auth::user() && Auth::user()->is_admin == 1) {

            return $next($request);

        }

        if ($request->wantsJson()) {

            $reply = [
                'meta' => [
                    'numRecords'   => 0,
                    'totalRows'    => 0,
                    'responseCode' => 401,
                    'message'      => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS,
                ],
                'data' => [],
            ];

            return response()->json($reply, 401);

        }

        return redirect('/dashboard');

    }
}
