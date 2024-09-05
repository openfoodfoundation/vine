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
        
    }
}
