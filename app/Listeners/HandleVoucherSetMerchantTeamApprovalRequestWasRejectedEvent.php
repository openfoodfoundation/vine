<?php

namespace App\Listeners;

use App\Events\VoucherSetMerchantTeamApprovalRequestWasRejected;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandleVoucherSetMerchantTeamApprovalRequestWasRejectedEvent
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
    public function handle(VoucherSetMerchantTeamApprovalRequestWasRejected $event): void
    {
        //
    }
}
