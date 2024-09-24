<?php

namespace App\Notifications\VoucherBeneficiaryDistributions;

use App\Models\Voucher;
use App\Models\VoucherBeneficiaryDistribution;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Storage;

class BeneficiaryVoucherDistributionEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param VoucherBeneficiaryDistribution $voucherBeneficiaryDistribution
     */
    public function __construct(public VoucherBeneficiaryDistribution $voucherBeneficiaryDistribution)
    {
    }

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

        $voucher = Voucher::find($this->voucherBeneficiaryDistribution->voucher_id);

        $mailMessage = (new MailMessage())
            ->subject('VINE: Your Voucher #' . $this->voucherBeneficiaryDistribution->voucher_id)
            ->line('When making an online purchase at a supported merchant, please use the following discount code:')
            ->line($voucher->voucher_short_code);

        $voucherTemplateStoragePath = '/voucher-sets/' .
            $this->voucherBeneficiaryDistribution->voucher_set_id . '/vouchers/individual/' .
            $this->voucherBeneficiaryDistribution->voucher_id . '/branded/voucher-branded.png';

        $brandedVoucherTemplateExists = Storage::exists($voucherTemplateStoragePath);
        if($brandedVoucherTemplateExists)
        {
            $brandedVoucherTemplateContents = Storage::get($voucherTemplateStoragePath);

            $mailMessage = $mailMessage->line('Please see attached for your VINE voucher. ')
                                       ->attachData(
                data   : $brandedVoucherTemplateContents,
                name   : 'voucher-' . $this->voucherBeneficiaryDistribution->voucher_id . '.png',
                options: [
                             'mime' => 'image/png',
                         ]
            );
        }

        $mailMessage = $mailMessage->line('Thank you for using our application!');


        return $mailMessage;
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
