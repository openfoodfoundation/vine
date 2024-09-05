<?php

namespace App\Console\Commands;

use App\Models\AuditItem;
use App\Models\PersonalAccessToken;
use App\Models\Team;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use App\Services\PersonalAccessTokenService;
use Illuminate\Console\Command;
use Lcobucci\JWT\Encoding\ChainedFormatter;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token\Builder;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test command. Do not run in production.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $model = PersonalAccessToken::find(5);
        $jwt = PersonalAccessTokenService::generateJwtForPersonalAccessToken($model);
        dd($jwt);
        $tokenBuilder = (new Builder(new JoseEncoder(), ChainedFormatter::default()));
        $algorithm    = new Sha256();
        $signingKey   = InMemory::plainText('BC0HYlfCbvFcn3BqbcwGkOdTOVilkdn3');

        $now   = new \DateTimeImmutable();
        $token = $tokenBuilder
            // Configures the issuer (iss claim)
            ->issuedBy(env('APP_URL'))
            // Configures the audience (aud claim)
//            ->permittedFor('http://example.org')
            // Configures the subject of the token (sub claim)
//            ->relatedTo('component1')
            // Configures the id (jti claim)
//            ->identifiedBy('4f1g23a12aa')
            // Configures the time that the token was issue (iat claim)
            ->issuedAt($now)
            // Configures the time that the token can be used (nbf claim)
//            ->canOnlyBeUsedAfter($now->modify('+1 minute'))
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify('+1 hour'))
            // Configures a new claim, called "uid"
//            ->withClaim('uid', 1)
            // Configures a new header, called "foo"
//            ->withHeader('foo', 'bar')
            // Builds a new token
            ->getToken($algorithm, $signingKey);

        echo $token->toString();
    }
}
