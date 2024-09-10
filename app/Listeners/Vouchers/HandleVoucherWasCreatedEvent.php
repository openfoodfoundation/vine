<?php

namespace App\Listeners\Vouchers;

use App\Events\Vouchers\VoucherWasUpdated;
use App\Jobs\Vouchers\AssignUniqueShortCodeToVoucherJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleVoucherWasCreatedEvent implements ShouldQueue
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
        dispatch(new AssignUniqueShortCodeToVoucherJob($event->voucher));
    }
}
