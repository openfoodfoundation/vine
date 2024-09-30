<?php

namespace App\Jobs\Vouchers;

use App\Models\Voucher;
use App\Services\VoucherService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AssignUniqueShortCodeToVoucherJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param Voucher $voucher
     */
    public function __construct(public Voucher $voucher) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $shortCode = VoucherService::generateRandomShortCode();
        $match     = Voucher::where('voucher_short_code', $shortCode)->first();

        while ($match) {
            $shortCode = VoucherService::generateRandomShortCode();
            $match     = Voucher::where('voucher_short_code', $shortCode)->first();
        }

        $this->voucher->voucher_short_code = $shortCode;
        $this->voucher->saveQuietly();
    }
}
