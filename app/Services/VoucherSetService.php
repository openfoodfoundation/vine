<?php

namespace App\Services;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;

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

        if (is_null($denominationArray) || count($denominationArray) == 0) {
            return false;
        }

        if (is_null($denominationArray)) {
            return false;
        }

        foreach ($denominationArray as $denominationListing) {

            if (
                !array_key_exists('value', $denominationListing) ||
                !array_key_exists('number', $denominationListing)
            ) {
                return false;
            }
        }

        return true;
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

        $voucherSet->num_vouchers            = Voucher::where('voucher_set_id', $voucherSet->id)->count();
        $voucherSet->total_set_value         = Voucher::where('voucher_set_id', $voucherSet->id)->sum('voucher_value_original');
        $voucherSet->num_voucher_redemptions = VoucherRedemption::where('voucher_set_id', $voucherSet->id)->count();

        $voucherSet->num_vouchers_fully_redeemed = Voucher::where('voucher_set_id', $voucherSet->id)
            ->where('voucher_value_remaining', 0)
            ->count();

        $voucherSet->num_vouchers_partially_redeemed = Voucher::where('voucher_set_id', $voucherSet->id)
            ->where('voucher_value_remaining', '>', 0)
            ->whereColumn('voucher_value_original', '!=', 'voucher_value_remaining')
            ->count();

        $voucherSet->num_vouchers_unredeemed = Voucher::where('voucher_set_id', $voucherSet->id)
            ->whereColumn('voucher_value_original', '=', 'voucher_value_remaining')
            ->count();

        $voucherSet->last_redemption_at        = VoucherRedemption::where('voucher_set_id', $voucherSet->id)->max('created_at');
        $voucherSet->total_set_value_remaining = self::calculateVoucherSetValueRemaining($voucherSet);
        $voucherSet->is_denomination_valid     = self::validateVoucherSetDenominations($voucherSet);

        $voucherSet->saveQuietly();
    }

    public static function generateDefaultVoucherSetName(VoucherSet $voucherSet): string
    {
        $voucherSetName = '';

        /**
         * Get the merchant teams initials
         */
        $merchantTeams               = VoucherSetMerchantTeam::with('merchantTeam')->where('voucher_set_id', $voucherSet->id)->get();
        $voucherSetTeamInitialsArray = [];
        foreach ($merchantTeams as $merchantTeam) {
            $voucherSetTeamInitialsArray[] = TeamService::generateTeamInitials($merchantTeam->merchantTeam);
        }

        $voucherSetName .= implode(' - ', $voucherSetTeamInitialsArray);

        /**
         * Append the service team name
         * Append the funding team name
         */
        $voucherSetName .= isset($voucherSet->allocatedToServiceTeam?->name) ? ' - ' . $voucherSet->allocatedToServiceTeam->name : ' - (No Service Team)';
        $voucherSetName .= isset($voucherSet->fundedByTeam?->name) ? ' - ' . $voucherSet->fundedByTeam->name : ' - (No Funding Team)';

        /**
         * Append the total set value in dollars
         */
        $voucherSetName .= ' - $' . number_format(($voucherSet->total_set_value / 100), 0, '.', '');
        $voucherSetName .= ' - ' . $voucherSet->created_at->format('d/m/Y');

        return $voucherSetName;
    }
}
