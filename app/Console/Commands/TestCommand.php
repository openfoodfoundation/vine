<?php

namespace App\Console\Commands;

use App\Models\Voucher;
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
    protected $description = 'Test command. Do not run in production unless you know what you are doing.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $vouchers = Voucher::with('voucherSet')->get();

        foreach ($vouchers as $voucher) {
            if (isset($voucher->voucherSet->expires_at)) {
                $voucher->expires_at = $voucher->voucherSet->expires_at;
                $voucher->save();
            }
        }

    }
}
