<?php

/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Jobs\VoucherSets;

use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateApprovalRequestsForListedMerchantsOnVoucherSet implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  VoucherSet  $voucherSet
     */
    public function __construct(public VoucherSet $voucherSet)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $merchantTeamIds = VoucherSetMerchantTeam::where('voucher_set_id', $this->voucherSet->id)
            ->pluck('merchant_team_id')
            ->toArray();

        foreach ($merchantTeamIds as $merchantTeamId) {
            $approval = VoucherSetMerchantTeamApprovalRequest::where('voucher_set_id', $this->voucherSet->id)
                ->where('merchant_team_id', $merchantTeamId)
                ->where('merchant_user_id', '??')
                ->first();

            if (!$approval) {

            }
        }
    }
}
