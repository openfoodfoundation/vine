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
        $user       = User::find($voucherSet->created_by_user_id);

        $user->notify(new VoucherSetMerchantTeamApprovalRequestRejectedNotification($this->request));
    }
}
