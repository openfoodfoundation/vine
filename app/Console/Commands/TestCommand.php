<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
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
        $users       = User::factory(100)->createQuietly();
        $teams       = Team::factory(100)->createQuietly();
        $vouchers    = Voucher::factory(100)->createQuietly();
        $voucherSets = VoucherSet::factory(100)->createQuietly();
    }
}
