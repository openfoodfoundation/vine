<?php

namespace App\Services;

use App\Models\PersonalAccessToken;
use DateTimeImmutable;
use Illuminate\Support\Facades\Crypt;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Builder;

class PersonalAccessTokenService
{
    /**
     * Generate a JWT based on a Personal Access Token
     *
     * @param PersonalAccessToken|\Laravel\Sanctum\PersonalAccessToken $personalAccessToken
     *
     * @return string
     */
    public static function generateJwtForPersonalAccessToken(PersonalAccessToken|\Laravel\Sanctum\PersonalAccessToken $personalAccessToken): string
    {
        $tokenBuilder       = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm          = new Sha256();
        $patDecryptedSecret = Crypt::decrypt($personalAccessToken->secret);
        $signingKey         = InMemory::plainText($patDecryptedSecret);
        $now                = new DateTimeImmutable();
        $token              = $tokenBuilder
            ->issuedBy(config('app.url'))
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 minute'))
            ->getToken($algorithm, $signingKey);

        return $token->toString();
    }
}
