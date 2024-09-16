<?php
/** @noinspection PhpUndefinedMethodInspection */

namespace App\Notifications\Mail\VoucherSetMerchantTeamApprovalRequest;

use App\Models\User;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use App\Services\BounceService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VoucherSetMerchantTeamApprovalRequestEmailNotification extends Notification
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     *
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        $user = User::find($notifiable->id);

        $urlApprove = BounceService::generateSignedUrlForUser(
            user        : $user,
            expiry      : now()->addDays(2),
            redirectPath: '/my-voucher-set-merchant-team-approval-request/' . $this->voucherSetMerchantTeamApprovalRequest->id . '?selected=approve'
        );
        $urlReject = BounceService::generateSignedUrlForUser(
            user        : $user,
            expiry      : now()->addDays(2),
            redirectPath: '/my-voucher-set-merchant-team-approval-request/' . $this->voucherSetMerchantTeamApprovalRequest->id . '?selected=reject'
        );

        $voucherSet = VoucherSet::with('createdByTeam')->find($this->voucherSetMerchantTeamApprovalRequest->voucher_set_id);

        return (new MailMessage())
            ->subject('A Vine voucher set is about to be been generated that may be redeemed at your shop')
            ->markdown('mail.voucher-set-approval-request', [
                'voucherSetId' => $this->voucherSetMerchantTeamApprovalRequest->voucher_set_id,
                'createdBy'    => $voucherSet->createdByTeam->name,
                'approve'      => $urlApprove,
                'reject'       => $urlReject,
            ]);
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
