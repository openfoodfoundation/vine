<?php

namespace App\Notifications\Mail\VoucherSetMerchantTeamApprovalRequest;

use App\Models\User;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VoucherSetMerchantTeamApprovalRequestRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param VoucherSetMerchantTeamApprovalRequest $request
     */
    public function __construct(public VoucherSetMerchantTeamApprovalRequest $request) {}

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
        $merchantUser = User::with('currentTeam')->find($this->request->merchant_user_id);

        return (new MailMessage())
            ->subject('A voucher set has been rejected by ' . $merchantUser->name)
            ->line($merchantUser->name . ' from ' . $merchantUser->currentTeam->name . ' has chosen to reject the approval of their merchant involvement in voucher set #' . $this->request->voucher_set_id . '.')
            ->line('Please liaise with your team to work out next steps - the Vine platform has not generated this voucher set at this point.')
            ->action('View voucher set', url('/voucher-set/' . $this->request->merchant_user_id))
            ->line('Thank you for using our application!');
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
