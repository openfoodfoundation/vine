<?php

namespace App\Listeners\VoucherSets;

use App\Events\VoucherSets\VoucherSetWasGenerated;
use App\Jobs\VoucherSets\SendVoucherSetGenerationEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleVoucherSetWasGeneratedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherSetWasGenerated $event
     */
    public function handle(VoucherSetWasGenerated $event): void
    {
        dispatch(new SendVoucherSetGenerationEmailNotification($event->voucherSet));
    }
}