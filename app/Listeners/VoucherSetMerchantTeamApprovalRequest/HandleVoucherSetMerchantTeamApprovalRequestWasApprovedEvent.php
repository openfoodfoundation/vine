<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace App\Listeners\VoucherSetMerchantTeamApprovalRequest;

use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasApproved;
use App\Models\User;
use App\Notifications\Slack\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestApprovedNotification;

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
    public function handle(VoucherSetMerchantTeamApprovalRequestWasApproved $event): void
    {
        $user = User::first();

        $user->notify(new VoucherSetMerchantTeamApprovalRequestApprovedNotification($event->request));
    }
}
