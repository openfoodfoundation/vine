<?php

namespace App\Notifications\Slack\VoucherSetMerchantTeamApprovalRequest;

use App\Models\User;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ActionsBlock;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ContextBlock;
use Illuminate\Notifications\Slack\SlackMessage;
use Illuminate\Support\Facades\URL;

class VoucherSetMerchantTeamApprovalRequestRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param VoucherSetMerchantTeamApprovalRequest $voucherSetMerchantTeamApprovalRequest
     */
    public function __construct(public VoucherSetMerchantTeamApprovalRequest $voucherSetMerchantTeamApprovalRequest) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        $rejectedUser = User::with('currentTeam')->find($this->voucherSetMerchantTeamApprovalRequest->merchant_user_id);
        $link         = URL::to('/admin/voucher-set/' . $this->voucherSetMerchantTeamApprovalRequest->voucher_set_id);

        return (new SlackMessage())
            ->headerBlock(':no_good: Voucher set merchant team approval request has been rejected!')
            ->contextBlock(function (ContextBlock $block) use ($rejectedUser) {
                $block->text('Voucher set #' . $this->voucherSetMerchantTeamApprovalRequest->voucher_set_id . ' has been rejected by ' . $rejectedUser->name . ' (' . $rejectedUser->currentTeam->name . ').');
            })
            ->actionsBlock(function (ActionsBlock $action) use ($link) {
                $action->button('Go to voucher set page')->url($link);
            });
    }

    /**
     * Get the array representation of the notification.
     *
     * @param object $notifiable
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

        ];
    }
}
