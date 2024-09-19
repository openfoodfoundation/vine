<?php

namespace App\Listeners\VoucherSetMerchantTeamApprovalRequest;

use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasRejected;
use App\Jobs\VoucherSetMerchantTeamApprovalRequest\SendVoucherSetMerchantTeamApprovalRequestEmailRejectionNotificationToStakeholders;
use App\Models\User;
use App\Notifications\Slack\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestRejectedNotification;
use App\Services\AuditItemService;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleVoucherSetMerchantTeamApprovalRequestWasRejectedEvent implements ShouldQueue
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

        $user->notify(new VoucherSetMerchantTeamApprovalRequestRejectedNotification($event->voucherSetMerchantTeamApprovalRequest));

        dispatch(new SendVoucherSetMerchantTeamApprovalRequestEmailRejectionNotificationToStakeholders($event->voucherSetMerchantTeamApprovalRequest));


        AuditItemService::createAuditItemForEvent(
            model    : $event->voucherSetMerchantTeamApprovalRequest->voucherSet,
            eventText: $event->voucherSetMerchantTeamApprovalRequest->merchantUser->name . ' rejected team merchant status for voucher set #'.$event->voucherSetMerchantTeamApprovalRequest->voucher_set_id,
            teamId   : $event->voucherSetMerchantTeamApprovalRequest->merchant_team_id
        );

    }
}
