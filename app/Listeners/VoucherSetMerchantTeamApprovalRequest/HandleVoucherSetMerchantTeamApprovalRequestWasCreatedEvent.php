<?php

namespace App\Listeners\VoucherSetMerchantTeamApprovalRequest;

use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasCreated;
use App\Jobs\VoucherSetMerchantTeamApprovalRequest\SendVoucherSetMerchantTeamApprovalRequestEmailNotification;
use App\Services\AuditItemService;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleVoucherSetMerchantTeamApprovalRequestWasCreatedEvent implements ShouldQueue
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
        dispatch(new SendVoucherSetMerchantTeamApprovalRequestEmailNotification($event->voucherSetMerchantTeamApprovalRequest));

        AuditItemService::createAuditItemForEvent(
            model    : $event->voucherSetMerchantTeamApprovalRequest->voucherSet,
            eventText: 'Merchant team approval requests were distributed for voucher set #' . $event->voucherSetMerchantTeamApprovalRequest->voucher_set_id,
            teamId   : $event->voucherSetMerchantTeamApprovalRequest->merchant_team_id
        );

    }
}
