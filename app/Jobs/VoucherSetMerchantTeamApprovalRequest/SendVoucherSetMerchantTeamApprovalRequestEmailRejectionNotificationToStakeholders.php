<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Jobs\VoucherSetMerchantTeamApprovalRequest;

use App\Models\User;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use App\Notifications\Mail\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestRejectedNotification;
use Illuminate\Foundation\Queue\Queueable;

class SendVoucherSetMerchantTeamApprovalRequestEmailRejectionNotificationToStakeholders
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param VoucherSetMerchantTeamApprovalRequest $request
     */
    public function __construct(public VoucherSetMerchantTeamApprovalRequest $request) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $voucherSet = VoucherSet::find($this->request->voucher_set_id);
        $teamUsers  = User::where('current_team_id', $voucherSet->created_by_team_id)->get();

        if ($teamUsers) {
            foreach ($teamUsers as $teamUser) {
                $teamUser->notify(new VoucherSetMerchantTeamApprovalRequestRejectedNotification($this->request));
            }
        }
    }
}
