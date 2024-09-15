<?php /** @noinspection PhpUndefinedMethodInspection */

namespace App\Jobs\VoucherSetMerchantTeamApprovalRequest;

use App\Models\User;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use App\Notifications\Slack\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestRejectedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendVoucherSetMerchantTeamApprovalRequestEmailRejectionNotificationToStakeholders implements ShouldQueue
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
        $user = User::first();

        $user->notify(new VoucherSetMerchantTeamApprovalRequestRejectedNotification($this->request));
    }
}
