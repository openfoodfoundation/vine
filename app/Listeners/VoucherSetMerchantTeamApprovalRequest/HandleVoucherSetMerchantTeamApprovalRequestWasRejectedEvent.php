<?php

namespace App\Listeners\VoucherSetMerchantTeamApprovalRequest;

use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasRejected;
use App\Jobs\VoucherSetMerchantTeamApprovalRequest\SendVoucherSetMerchantTeamApprovalRequestEmailRejectionNotificationToStakeholders;
use App\Models\User;
use App\Notifications\Slack\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestRejectedNotification;

class HandleVoucherSetMerchantTeamApprovalRequestWasRejectedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherSetMerchantTeamApprovalRequestWasRejected $event
     */
    public function handle(VoucherSetMerchantTeamApprovalRequestWasRejected $event): void
    {
        $user = User::first();

        $user->notify(new VoucherSetMerchantTeamApprovalRequestRejectedNotification($event->request));

        dispatch(new SendVoucherSetMerchantTeamApprovalRequestEmailRejectionNotificationToStakeholders($event->request));
    }
}
