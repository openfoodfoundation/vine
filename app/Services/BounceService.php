<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;

/**
 * This service works with the `/bounce` route to enable users
 * to be securely logged in, verified and redirected if
 * they have the correct URL and signature
 */
class BounceService
{
    /**
     * Generate a signed URL for a user that will redirect them to a given url, after validation.
     *
     * @param User        $user
     * @param Carbon|null $expiry
     * @param string      $redirectPath
     *
     * @return string
     */
    public static function generateSignedUrlForUser(User $user, ?Carbon $expiry, string $redirectPath): string
    {
        /**
         * @see Route called "bounce" for how this is parsed
         */
        return URL::signedRoute(
            name      : 'bounce',
            parameters: [
                'id'           => Crypt::encrypt($user->id),
                'redirectPath' => $redirectPath . '?selected=approve',
            ],
            expiration: $expiry
        );

    }
}
