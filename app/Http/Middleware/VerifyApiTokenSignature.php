<?php

namespace App\Http\Middleware;

use App\Enums\ApiResponse;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Lcobucci\JWT\Encoding\CannotDecodeContent;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\InvalidTokenStructure;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Token\UnsupportedHeaderFound;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Validator;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiTokenSignature
{
    /**
     * Handle an incoming request.
     *
     * @param Request                                                                         $request
     * @param Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allow        = true;
        $errorMessage = ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS;

        /**
         * If the request is not from the front end, the requests must be signed using a JWT
         */
        if (!EnsureFrontendRequestsAreStateful::fromFrontend($request)) {

            if (!$request->user() || !$request->user()->currentAccessToken()) {
                $allow = false;
            }

            if (!$request->hasHeader('X-AUTHORIZATION')) {
                $allow        = false;
                $errorMessage = ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT->value . ' ' . ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_JWT_HEADER_REQUIRED->value;
            }
            else {

                try {

                    $jwt         = $request->header('X-AUTHORIZATION');
                    $jwtBits     = explode(' ', $jwt);
                    $jwtContents = end($jwtBits);
                    $parser      = new Parser(new JoseEncoder());
                    $token       = $parser->parse(
                        $jwtContents
                    );
                    $accessTokenSecret = Crypt::decrypt($request->user()->currentAccessToken()->secret);
                    $validator         = new Validator();
                    $signingKey        = InMemory::plainText($accessTokenSecret);

                    if ($token->isExpired(now())) {
                        $allow        = false;
                        $errorMessage = ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT->value . ' ' . ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_EXPIRED->value;
                    }
                    elseif (!$token->claims()->has('iat')) {
                        $allow        = false;
                        $errorMessage = ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT->value . ' ' . ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_IAT_CLAIM_REQUIRED->value;
                    }
                    elseif (!$token->claims()->has('exp')) {
                        $allow        = false;
                        $errorMessage = ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT->value . ' ' . ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_EXP_CLAIM_REQUIRED->value;
                    }
                    else {

                        $iat       = $token->claims()->get('iat');
                        $iatCarbon = Carbon::parse($iat);

                        $exp       = $token->claims()->get('exp');
                        $expCarbon = Carbon::parse($exp);

                        if ($iatCarbon->isBefore(now()->subMinute())) {
                            $allow        = false;
                            $errorMessage = ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT->value . ' ' . ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_IAT_EXPIRED->value;
                        }
                        elseif ($expCarbon->isAfter($iatCarbon->addMinute())) {
                            $allow        = false;
                            $errorMessage = ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT->value . ' ' . ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT_IAT_EXP_TOO_LARGE->value;
                        }
                        else {
                            $isValid = $validator->validate($token, new SignedWith(new Sha256(), $signingKey));

                            if (!$isValid) {
                                $allow        = false;
                                $errorMessage = ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT;
                            }

                            /**
                             * The JWT signature is valid, based on the access token's secret
                             */
                        }

                    }

                }
                catch (CannotDecodeContent|InvalidTokenStructure|UnsupportedHeaderFound $e) {
                    $allow        = false;
                    $errorMessage = ApiResponse::RESPONSE_AUTHORIZATION_SIGNATURE_INCORRECT;
                }

            }

        }

        if (!$allow) {
            $reply = [
                'meta' => [
                    'numRecords'   => 0,
                    'totalRows'    => 0,
                    'responseCode' => 401,
                    'message'      => $errorMessage,
                ],
                'data' => [],
            ];

            return response()->json($reply, 401);
        }

        return $next($request);

    }
}
