<?php

namespace App\Listeners\VoucherSets;

use App\Events\VoucherSets\VoucherSetWasGenerated;
use App\Jobs\VoucherSets\SendVoucherSetGenerationEmailNotification;
use App\Models\User;
use App\Notifications\Mail\VoucherSets\VoucherSetGenerationSuccessEmailNotification;
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
        /**
         * Notify the members of the service team
         */
        dispatch(new SendVoucherSetGenerationEmailNotification($event->voucherSet));

        /**
         * Notify the person who created the set
         */
        $notificationUser = User::find($event->voucherSet->created_by_user_id);
        $notificationUser?->notify(new VoucherSetGenerationSuccessEmailNotification(voucherSet: $event->voucherSet));
    }
}
