<?php

namespace App\Listeners\VoucherSetMerchantTeamApprovalRequest;

use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasCreated;
use App\Jobs\VoucherSetMerchantTeamApprovalRequest\SendVoucherSetMerchantTeamApprovalRequestEmailNotification;

class HandleVoucherSetMerchantTeamApprovalRequestWasCreatedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherSetMerchantTeamApprovalRequestWasCreated $event
     */
    public function handle(VoucherSetMerchantTeamApprovalRequestWasCreated $event): void
    {
        dispatch(new SendVoucherSetMerchantTeamApprovalRequestEmailNotification($event->request));
    }
}
