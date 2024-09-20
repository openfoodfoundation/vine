<?php

namespace App\Notifications\VoucherBeneficiaryDistributions;

use App\Models\VoucherBeneficiaryDistribution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BeneficiaryVoucherDistributionEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param VoucherBeneficiaryDistribution $voucherBeneficiaryDistribution
     */
    public function __construct(public VoucherBeneficiaryDistribution $voucherBeneficiaryDistribution) {}

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
        return (new MailMessage())
            ->line('Your voucher has been distributed to you.')
            ->line('You can access it by scanning the QR code below.')
            ->action('Notification Action', url('/'))
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
