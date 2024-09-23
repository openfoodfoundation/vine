<?php

/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Jobs\VoucherSets;

use App\Models\TeamUser;
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

            $merchantTeamUsers = TeamUser::where('team_id', $merchantTeamId)->get();

            foreach ($merchantTeamUsers as $merchantTeamUser) {

                $approval = VoucherSetMerchantTeamApprovalRequest::where('voucher_set_id', $this->voucherSet->id)
                    ->where('merchant_team_id', $merchantTeamId)
                    ->where('merchant_user_id', $merchantTeamUser->user_id)
                    ->first();

                /**
                 * Only create a new approval request if one does not already exist
                 */
                if (!$approval) {
                    $newApproval                   = new VoucherSetMerchantTeamApprovalRequest();
                    $newApproval->voucher_set_id   = $this->voucherSet->id;
                    $newApproval->merchant_team_id = $merchantTeamId;
                    $newApproval->merchant_user_id = $merchantTeamUser->user_id;
                    $newApproval->save();
                }
            }

        }
    }
}
