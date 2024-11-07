<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Listeners\VoucherSetMerchantTeamApprovalRequest;

use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasApproved;
use App\Models\User;
use App\Models\VoucherSetMerchantTeam;
use App\Notifications\Slack\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestApprovedNotification;
use App\Services\AuditItemService;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleVoucherSetMerchantTeamApprovalRequestWasApprovedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param VoucherSetMerchantTeamApprovalRequestWasApproved $event
     */
    public function handle(VoucherSetMerchantTeamApprovalRequestWasApproved $event): void
    {

        /**
         * Mark the participating merchant team with the ID of the approval
         */
        $voucherSetMerchantTeam = VoucherSetMerchantTeam::where(
            column  : 'voucher_set_id',
            operator: $event->voucherSetMerchantTeamApprovalRequest->voucher_set_id
        )->where(
            column  : 'merchant_team_id',
            operator: $event->voucherSetMerchantTeamApprovalRequest->merchant_team_id
        )->first();

        if($voucherSetMerchantTeam)
        {
            $voucherSetMerchantTeam->voucher_set_merchant_team_approval_request_id = $event->voucherSetMerchantTeamApprovalRequest->id;
            $voucherSetMerchantTeam->save();
        }

        /**
         * Notify the user
         */
        $user = User::first();
        $user->notify(new VoucherSetMerchantTeamApprovalRequestApprovedNotification($event->voucherSetMerchantTeamApprovalRequest));

        AuditItemService::createAuditItemForEvent(
            model    : $event->voucherSetMerchantTeamApprovalRequest->voucherSet,
            eventText: $event->voucherSetMerchantTeamApprovalRequest->merchantUser->name . ' approved merchant status for voucher set #' . $event->voucherSetMerchantTeamApprovalRequest->voucher_set_id,
            teamId   : $event->voucherSetMerchantTeamApprovalRequest->merchant_team_id
        );
    }
}
