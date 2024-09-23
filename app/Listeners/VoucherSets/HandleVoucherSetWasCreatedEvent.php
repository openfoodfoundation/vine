<?php

namespace App\Listeners\VoucherSets;

use App\Events\Vouchers\VoucherWasCreated;
use App\Events\VoucherSets\VoucherSetWasCreated;
use App\Jobs\VoucherSets\CreateApprovalRequestsForListedMerchantsOnVoucherSet;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleVoucherSetWasCreatedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherWasCreated $event
     */
    public function handle(VoucherSetWasCreated $event): void
    {

        if (config('app.env') !== 'testing') {
            dispatch(new CreateApprovalRequestsForListedMerchantsOnVoucherSet($event->voucherSet));
        }

    }
}
