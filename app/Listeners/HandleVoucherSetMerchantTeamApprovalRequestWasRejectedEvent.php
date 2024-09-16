<?php

namespace App\Listeners;

use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasRejected;
use App\Jobs\VoucherSetMerchantTeamApprovalRequest\SendVoucherSetMerchantTeamApprovalRequestEmailRejectionNotificationToStakeholders;

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
        dispatch(new SendVoucherSetMerchantTeamApprovalRequestEmailRejectionNotificationToStakeholders($event->request));
    }
}
