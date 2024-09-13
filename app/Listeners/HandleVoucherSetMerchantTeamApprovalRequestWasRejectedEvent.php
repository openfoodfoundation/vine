<?php

namespace App\Listeners;

use App\Events\VoucherSetMerchantTeamApprovalRequestWasRejected;

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
    public function handle(VoucherSetMerchantTeamApprovalRequestWasRejected $event): void {}
}
