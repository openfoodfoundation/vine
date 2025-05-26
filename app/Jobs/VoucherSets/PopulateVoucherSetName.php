<?php

namespace App\Jobs\VoucherSets;

use App\Models\VoucherSet;
use App\Services\VoucherSetService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class PopulateVoucherSetName implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param VoucherSet $voucherSet
     */
    public function __construct(public VoucherSet $voucherSet) {}

    /**
     * Execute the job. Populate the voucher set name but only if it is null.
     */
    public function handle(): void
    {
        if (is_null($this->voucherSet->name)) {
            $this->voucherSet->name = VoucherSetService::generateDefaultVoucherSetName($this->voucherSet);
            $this->voucherSet->saveQuietly();
        }
    }
}
