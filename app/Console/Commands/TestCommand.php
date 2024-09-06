<?php

namespace App\Console\Commands;

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
        $me = User::find(3);

        $voucherSet = VoucherSet::factory()->createQuietly([
            'created_by_team_id' => $me->current_team_id,
            'created_by_user_id' => $me->id,
        ]);

        $voucher = Voucher::factory()->createQuietly([
            'voucher_set_id' => $voucherSet->id,
        ]);

    }
}
