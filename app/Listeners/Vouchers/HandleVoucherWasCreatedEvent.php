<?php

namespace App\Listeners\Vouchers;

use App\Events\Vouchers\VoucherWasCreated;
use App\Jobs\Vouchers\AssignUniqueShortCodeToVoucherJob;
use App\Jobs\Vouchers\CollateVoucherAggregatesJob;
use App\Jobs\Vouchers\GenerateStorageVoucherQrCode;
use App\Jobs\VoucherSets\CollateVoucherSetAggregatesJob;
use App\Models\VoucherSet;
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

        $voucherSet = VoucherSet::find($event->voucher->voucher_set_id);
        if ($voucherSet) {
            dispatch(new CollateVoucherSetAggregatesJob($voucherSet));
        }

        if (config('app.env') != 'testing') {
            /**
             * Delay this job by 5 minutes so that the image generation comes last.
             */
            dispatch(new GenerateStorageVoucherQrCode($event->voucher))->delay(now()->addMinutes(10));
        }
    }
}
