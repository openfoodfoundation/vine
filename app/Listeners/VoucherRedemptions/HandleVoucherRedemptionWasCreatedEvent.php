<?php

namespace App\Listeners\VoucherRedemptions;

use App\Events\VoucherRedemptions\VoucherRedemptionWasCreated;
use App\Jobs\Vouchers\CollateVoucherAggregatesJob;
use App\Jobs\VoucherSets\CollateVoucherSetAggregatesJob;
use App\Models\Voucher;
use App\Models\VoucherSet;
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

            $voucher->last_redemption_at = now();
            $voucher->saveQuietly();

            dispatch(new CollateVoucherAggregatesJob($voucher));

        }

        $voucherSet = VoucherSet::find($event->voucherRedemption->voucher_set_id);

        if ($voucherSet) {

            $voucherSet->last_redemption_at = now();
            $voucherSet->saveQuietly();

            dispatch(new CollateVoucherSetAggregatesJob($voucherSet));

        }

    }
}
