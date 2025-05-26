<?php

namespace App\Listeners\VoucherSets;

use App\Events\VoucherSets\VoucherSetWasCreated;
use App\Jobs\VoucherSets\PopulateVoucherSetName;
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
     * @param VoucherSetWasCreated $event
     */
    public function handle(VoucherSetWasCreated $event): void
    {

        /**
         * Ensure the expiry is at the very end of the day
         */
        if (!is_null($event->voucherSet->expires_at)) {
            $event->voucherSet->expires_at = $event->voucherSet->expires_at->endOfDay();
            $event->voucherSet->saveQuietly();
        }

    }
}
