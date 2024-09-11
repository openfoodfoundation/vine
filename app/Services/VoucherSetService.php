<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;

class VoucherSetService
{
    /**
     * @param VoucherSet $voucherSet
     *
     * @return int
     */
    public static function calculateVoucherSetValueRemaining(VoucherSet $voucherSet): int
    {
        $redeemedAmount = VoucherRedemption::where('voucher_set_id', $voucherSet->id)->sum('redeemed_amount');

        return $voucherSet->total_set_value - $redeemedAmount;
    }

    /**
     * @param VoucherSet $voucherSet
     */
    public static function collateVoucherSetAggregates(VoucherSet $voucherSet): void
    {
        $voucherSet->num_vouchers              = Voucher::where('voucher_set_id', $voucherSet->id)->count();
        $voucherSet->num_voucher_redemptions   = VoucherRedemption::where('voucher_set_id', $voucherSet->id)->count();
        $voucherSet->last_redemption_at        = VoucherRedemption::where('voucher_set_id', $voucherSet->id)->max('created_at');
        $voucherSet->total_set_value_remaining = self::calculateVoucherSetValueRemaining($voucherSet);

        $voucherSet->saveQuietly();
    }
}
