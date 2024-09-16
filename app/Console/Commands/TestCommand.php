<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;

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

        $me = User::find(1);

        $myUrl = URL::temporarySignedRoute(
            'bounce',
            now()->addDays(2),
            [
                'id'           => Crypt::encrypt($me->id),
                'redirectPath' => '/my-team',
            ]
        );

        dd($myUrl);

    }
}
