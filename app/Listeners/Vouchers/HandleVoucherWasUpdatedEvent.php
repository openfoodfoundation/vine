<?php

namespace App\Listeners\Vouchers;

use App\Events\Vouchers\VoucherWasUpdated;
use App\Jobs\Vouchers\CollateVoucherAggregatesJob;
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
    }
}
