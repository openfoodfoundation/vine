<?php

namespace App\Listeners\VoucherRedemptions;

use App\Events\VoucherRedemptions\VoucherRedemptionWasCreated;
use App\Models\Voucher;
use App\Services\VoucherService;
use Exception;

class HandleVoucherRedemptionWasCreatedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherRedemptionWasCreated $event
     *
     * @throws Exception
     */
    public function handle(VoucherRedemptionWasCreated $event): void
    {
        $voucher = Voucher::find($event->voucherRedemption->voucher_id);

        if ($voucher) {

            VoucherService::updateVoucherAmountRemaining($voucher);

            if ($voucher->voucher_value_remaining <= 0) {
                $voucher->voucher_short_code = null;
                $voucher->saveQuietly();
            }
        }
    }
}
