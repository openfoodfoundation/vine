<?php

namespace App\Console\Commands\VoucherSets;

use App\Jobs\Vouchers\GenerateStorageVoucherQrCode;
use App\Models\Voucher;
use App\Models\VoucherSet;
use Illuminate\Console\Command;

use function Laravel\Prompts\text;

class RegenerateVoucherSetQrCodesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:regenerate-voucher-set-qr-codes-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asks for a voucher set ID and sends all vouchers for QR code regeneration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $voucherSetId = text('Voucher Set ID?');

        $voucherSet = VoucherSet::find($voucherSetId);

        if ($voucherSet) {
            $this->line('Nice one - sending all vouchers up for QR regeneration.');

            $vouchers = Voucher::where('voucher_set_id', $voucherSetId)->get();

            $progressBar = $this->output->createProgressBar($vouchers->count());
            $progressBar->start();
            foreach ($vouchers as $voucher) {
                $progressBar->advance(1);

                dispatch(new GenerateStorageVoucherQrCode($voucher));
            }

            $progressBar->finish();
        }

    }
}
