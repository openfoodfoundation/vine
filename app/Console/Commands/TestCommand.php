<?php

namespace App\Console\Commands;

use App\Models\PersonalAccessToken;
use App\Services\PersonalAccessTokenService;
use Illuminate\Console\Command;

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
        $jwt   = PersonalAccessTokenService::generateJwtForPersonalAccessToken($model);
        dd($jwt);

    }
}
