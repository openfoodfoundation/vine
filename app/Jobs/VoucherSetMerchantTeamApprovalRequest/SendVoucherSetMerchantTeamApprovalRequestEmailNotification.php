<?php

namespace App\Jobs\VoucherSetMerchantTeamApprovalRequest;

use App\Models\VoucherSetMerchantTeamApprovalRequest;
use App\Notifications\Mail\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendVoucherSetMerchantTeamApprovalRequestEmailNotification implements ShouldQueue
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
        $merchantUserId = $this->request->merchant_user_id;

        $merchantUserId->notify(new VoucherSetMerchantTeamApprovalRequestEmailNotification($this->request));

    }
}
