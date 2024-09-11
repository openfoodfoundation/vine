<?php

namespace App\Jobs\Vouchers;

use App\Models\Voucher;
use App\Services\VoucherService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CollateVoucherAggregatesJob implements ShouldQueue
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
     *
     * @throws Exception
     */
    public function handle(): void
    {
        VoucherService::updateVoucherAmountRemaining($this->voucher);
        VoucherService::collateVoucherAggregates($this->voucher);
    }
}
