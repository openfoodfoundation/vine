<?php

namespace App\Jobs\VoucherSetMerchantTeams;

use App\Models\TeamUser;
use App\Models\VoucherSetMerchantTeam;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class CreateVoucherSetMerchantTeamApprovalRequests implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param VoucherSetMerchantTeam $voucherSetMerchantTeam
     */
    public function __construct(public VoucherSetMerchantTeam $voucherSetMerchantTeam) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $merchantTeamUsers = TeamUser::where('team_id', $this->voucherSetMerchantTeam->merchant_team_id)->get();

        foreach ($merchantTeamUsers as $merchantTeamUser) {

            $approval = VoucherSetMerchantTeamApprovalRequest::where('voucher_set_id', $this->voucherSetMerchantTeam->voucher_set_id)
                ->where('merchant_team_id', $this->voucherSetMerchantTeam->merchant_team_id)
                ->where('merchant_user_id', $merchantTeamUser->user_id)
                ->first();

            /**
             * Only create a new approval request if one does not already exist
             */
            if (!$approval) {
                $newApproval                   = new VoucherSetMerchantTeamApprovalRequest();
                $newApproval->voucher_set_id   = $this->voucherSetMerchantTeam->voucher_set_id;
                $newApproval->merchant_team_id = $this->voucherSetMerchantTeam->merchant_team_id;
                $newApproval->merchant_user_id = $merchantTeamUser->user_id;
                $newApproval->save();
            }
        }
    }

    public function shouldQueue(): bool
    {
        return config('app.env') !== 'testing';
    }
}
