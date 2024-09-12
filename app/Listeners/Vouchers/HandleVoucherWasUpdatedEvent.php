<?php

namespace App\Listeners\Vouchers;

use App\Events\Vouchers\VoucherWasUpdated;
use App\Jobs\Vouchers\CollateVoucherAggregatesJob;
use App\Jobs\VoucherSets\CollateVoucherSetAggregatesJob;
use App\Models\VoucherSet;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleVoucherWasUpdatedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherWasUpdated $event
     */
    public function handle(VoucherWasUpdated $event): void
    {
        dispatch(new CollateVoucherAggregatesJob($event->voucher));

        $voucherSet = VoucherSet::find($event->voucher->voucher_set_id);

        if ($voucherSet) {

            dispatch(new CollateVoucherSetAggregatesJob($voucherSet));

        }
    }
}
