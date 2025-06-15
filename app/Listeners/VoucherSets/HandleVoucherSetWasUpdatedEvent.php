<?php

namespace App\Listeners\VoucherSets;

use App\Events\VoucherSets\VoucherSetWasUpdated;
use App\Jobs\VoucherSets\CollateVoucherSetAggregatesJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleVoucherSetWasUpdatedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherSetWasUpdated $event
     */
    public function handle(VoucherSetWasUpdated $event): void
    {
        dispatch(new CollateVoucherSetAggregatesJob($event->voucherSet));

    }
}
