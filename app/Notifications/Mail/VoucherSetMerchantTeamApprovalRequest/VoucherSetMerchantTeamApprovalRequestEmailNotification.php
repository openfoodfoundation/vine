<?php

namespace App\Notifications\Mail\VoucherSetMerchantTeamApprovalRequest;

use App\Models\User;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use App\Services\BounceService;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VoucherSetMerchantTeamApprovalRequestEmailNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param VoucherSetMerchantTeamApprovalRequest $voucherSetMerchantTeamApprovalRequest
     * @param VoucherSetMerchantTeamApprovalRequest $request
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
            redirectPath: '/voucher-set-merchant-team-approval-request-approved/' . $this->voucherSetMerchantTeamApprovalRequest->id
        );
        $urlReject = BounceService::generateSignedUrlForUser(
            user        : $user,
            expiry      : now()->addDays(2),
            redirectPath: '/voucher-set-merchant-team-approval-request-approved/' . $this->voucherSetMerchantTeamApprovalRequest->id
        );

        $urlReject = URL::temporarySignedRoute(
            'voucher-set-merchant-team-approval-request-rejected',
            now()->addDays(2),
            ['id' => $this->request->id]
        );

        $voucherSet = VoucherSet::with('createdByTeam')->find($this->request->voucher_set_id);

        return (new MailMessage())
            ->subject('A Vine voucher set is about to be been generated that may be redeemed at your shop')
            ->markdown('mail.voucher-set-approval-request', [
                'voucherSetId' => $this->request->voucher_set_id,
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
