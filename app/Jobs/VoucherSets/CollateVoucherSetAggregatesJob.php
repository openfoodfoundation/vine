<?php

namespace App\Jobs\VoucherSets;

use App\Models\VoucherSet;
use App\Services\VoucherSetService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CollateVoucherSetAggregatesJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param VoucherSet $voucherSet
     */
    public function __construct(public VoucherSet $voucherSet) {}

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        VoucherSetService::collateVoucherSetAggregates($this->voucherSet);
    }
}
