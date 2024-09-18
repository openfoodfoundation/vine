<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;

class VoucherSetService
{
    /**
     * Determine if a voucher set's JSON is valid, and matched its value
     *
     * @param VoucherSet $voucherSet
     *
     * @return mixed
     */
    public static function validateVoucherSetDenominations(VoucherSet $voucherSet): bool
    {
        $denominationArray = json_decode($voucherSet->denomination_json, true);

        $totalValue = 0;

        if (!is_null($denominationArray)) {
            foreach ($denominationArray as $denominationListing) {
                $totalValue += ($denominationListing['value'] * $denominationListing['number']);
            }
        }

        return $totalValue == $voucherSet->total_set_value;
    }

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
        $voucherSet->is_denomination_valid = self::validateVoucherSetDenominations($voucherSet);
        $voucherSet->num_vouchers              = Voucher::where('voucher_set_id', $voucherSet->id)->count();
        $voucherSet->total_set_value           = Voucher::where('voucher_set_id', $voucherSet->id)->sum('voucher_value_original');
        $voucherSet->num_voucher_redemptions   = VoucherRedemption::where('voucher_set_id', $voucherSet->id)->count();
        $voucherSet->last_redemption_at        = VoucherRedemption::where('voucher_set_id', $voucherSet->id)->max('created_at');
        $voucherSet->total_set_value_remaining = self::calculateVoucherSetValueRemaining($voucherSet);

        $voucherSet->saveQuietly();
    }
}
