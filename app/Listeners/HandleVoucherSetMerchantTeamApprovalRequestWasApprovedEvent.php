<?php

namespace App\Listeners;

use App\Events\VoucherSetMerchantTeamApprovalRequestWasApproved;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleVoucherSetMerchantTeamApprovalRequestWasApprovedEvent
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
    public function handle(VoucherSetMerchantTeamApprovalRequestWasApproved $event): void
    {
        //
    }
}
