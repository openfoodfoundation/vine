<?php

namespace App\Listeners;

use App\Events\VoucherSetMerchantTeamApprovalRequestWasApproved;

class HandleVoucherSetMerchantTeamApprovalRequestWasApprovedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherSetMerchantTeamApprovalRequestWasApproved $event
     */
    public function handle(VoucherSetMerchantTeamApprovalRequestWasApproved $event): void {}
}
