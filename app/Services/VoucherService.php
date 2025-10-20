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
        $voucher->saveQuietly();

    }

    /**
     * @param Voucher $voucher
     *
     * @throws Exception
     */
    public static function collateVoucherAggregates(Voucher $voucher): void
    {
        self::updateVoucherAmountRemaining($voucher);
        $voucher->refresh();

        if ($voucher->voucher_value_remaining <= 0) {
            $voucher->voucher_short_code = null;
        }

        if ($voucher->voucher_value_remaining != $voucher->voucher_value_original) {
            $voucher->num_voucher_redemptions = VoucherRedemption::where('voucher_id', $voucher->id)->count();
            $voucher->last_redemption_at      = VoucherRedemption::where('voucher_id')->max('created_at');
        }

        $voucher->saveQuietly();
    }

    /**
     * Generates a random short code for a voucher of the format: [A-Z][A-Z][0-9][0-9][0-9][0-9].
     * AKA: Two uppercase letters followed by four digits.
     *
     * This function is not for ASSIGNING the short code, it only GENERATES it.
     * The functionality is isolated for testing purposes. The assignment occurs in the following job.
     *
     * @return string
     */
    private static function generateRandomShortCode(): string
    {
        $availableLetters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

        /**
         * Selects two letters at random from the available letters
         */
        $firstLetter  = $availableLetters[rand(0, strlen($availableLetters) - 1)];
        $secondLetter = $availableLetters[rand(0, strlen($availableLetters) - 1)];

        /**
         * Picks a random number between 0 and 9999.
         * If the number is less than 4 digits long it
         * pads the number with leading zeros.
         */
        $numbers = sprintf('%04d', mt_rand(0, 9999));

        return $firstLetter . $secondLetter . $numbers;
    }

    public static function findUniqueShortCodeForVoucher(): string
    {
        $shortCode = self::generateRandomShortCode();
        $match     = Voucher::where('voucher_short_code', $shortCode)->exists();

        while ($match) {
            $shortCode = self::generateRandomShortCode();
            $match     = Voucher::where('voucher_short_code', $shortCode)->exists();
        }

        return $shortCode;
    }
}
