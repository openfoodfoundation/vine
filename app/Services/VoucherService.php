<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use Exception;
use Illuminate\Support\Facades\Log;

class VoucherService
{
    /**
     * @param Voucher $voucher
     *
     * @return int
     */
    public static function calculateVoucherAmountRedeemed(Voucher $voucher): int
    {
        return VoucherRedemption::where('voucher_id', $voucher->id)->sum('redeemed_amount');
    }

    /**
     * @param Voucher $voucher
     *
     * @return int
     */
    public static function calculateVoucherAmountRemaining(Voucher $voucher): int
    {
        $redeemedAmount = self::calculateVoucherAmountRedeemed($voucher);

        return $voucher->voucher_value_original - $redeemedAmount;
    }

    /**
     * @param Voucher $voucher
     *
     * @throws Exception
     */
    public static function updateVoucherAmountRemaining(Voucher $voucher): void
    {
        $remainingAmount = self::calculateVoucherAmountRemaining($voucher);

        if ($remainingAmount < 0) {
            $message = 'Voucher ' . $voucher->id . ' has had too many redemptions';

            Log::critical($message);
            throw new Exception($message);
        }

        $voucher->voucher_value_remaining = $remainingAmount;
        $voucher->save();

    }

    /**
     * @param Voucher $voucher
     *
     * @throws Exception
     */
    public static function collateVoucherAggregates(Voucher $voucher): void
    {
        $voucher->voucher_value_remaining = self::calculateVoucherAmountRemaining($voucher);
        $voucher->num_voucher_redemptions = VoucherRedemption::where('voucher_id', $voucher->id)->count();
        $voucher->save();
    }
}
