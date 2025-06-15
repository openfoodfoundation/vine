<?php

namespace App\Console\Commands;

use App\Jobs\VoucherSets\PopulateVoucherSetName;
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
    protected $description = 'Test command. Do not run in production unless you know what you are doing.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $voucherSets = VoucherSet::all();

        foreach ($voucherSets as $voucherSet) {
            $voucherSet->name = null;
            $voucherSet->saveQuietly();

            dispatch(new PopulateVoucherSetName($voucherSet));
        }

    }
}
