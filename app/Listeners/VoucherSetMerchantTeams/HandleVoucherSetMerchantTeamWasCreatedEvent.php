<?php

namespace App\Listeners\VoucherSetMerchantTeams;

use App\Events\VoucherSetMerchantTeams\VoucherSetMerchantTeamWasCreated;
use App\Jobs\VoucherSetMerchantTeams\CreateVoucherSetMerchantTeamApprovalRequests;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleVoucherSetMerchantTeamWasCreatedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(VoucherSetMerchantTeamWasCreated $event): void
    {
        /**
         * Create & send the approval requests to the merchant users
         */
        dispatch(new CreateVoucherSetMerchantTeamApprovalRequests($event->voucherSetMerchantTeam));
    }
}
