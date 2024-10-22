<?php

namespace App\Listeners\VoucherSetMerchantTeams;

use App\Events\VoucherSetMerchantTeams\VoucherSetMerchantTeamWasCreated;
use App\Jobs\VoucherSetMerchantTeams\CreateVoucherSetMerchantTeamApprovalRequests;

class HandleVoucherSetMerchantTeamWasCreatedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherSetMerchantTeamWasCreated $event
     */
    public function handle(VoucherSetMerchantTeamWasCreated $event): void
    {
        /**
         * Create & send the approval requests to the merchant users
         */
        dispatch(new CreateVoucherSetMerchantTeamApprovalRequests($event->voucherSetMerchantTeam));
    }
}
