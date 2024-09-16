<?php

namespace App\Notifications\Mail\VoucherSets;

use App\Models\VoucherSet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VoucherSetGenerationEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param VoucherSet $voucherSet
     */
    public function __construct(public VoucherSet $voucherSet) {}

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
     * @return MailMessage
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('A Vine voucher set has been generated for your Service')
            ->line('A Vine voucher set has been approved by merchants and generated for your Service.')
            ->line('Please see the details on the voucher set page.')
            ->action('View voucher set', url('/voucher-set/' . $this->voucherSet->id))
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
