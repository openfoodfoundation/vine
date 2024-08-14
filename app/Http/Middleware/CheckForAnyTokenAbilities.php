<?php

namespace App\Http\Middleware;

use App\Enums\ApiResponse;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\Exceptions\MissingAbilityException;

class CheckForAnyTokenAbilities
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed   ...$abilities
     *
     * @return JsonResponse
     *
     * @throws AuthenticationException|MissingAbilityException
     */
    public function handle($request, $next, ...$abilities)
    {

        if (!$request->user() || !$request->user()->currentAccessToken()) {
            throw new AuthenticationException();
        }

        /**
         * If the token has any of the required abilities
         */
        foreach ($abilities as $ability) {
            if ($request->user()->tokenCan($ability)) {
                return $next($request);
            }
        }

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
}
