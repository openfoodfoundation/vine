<?php

namespace App\Listeners\Vouchers;

use App\Events\Vouchers\VoucherWasCreated;
use App\Jobs\Vouchers\GenerateStorageVoucherQrCode;
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
     * @param VoucherWasCreated $event
     */
    public function handle(VoucherWasCreated $event): void
    {

        if (config('app.env') != 'testing') {
            /**
             * Delay this job by 5 minutes so that the image generation comes last.
             */
            dispatch(new GenerateStorageVoucherQrCode($event->voucher));
        }
    }
}
