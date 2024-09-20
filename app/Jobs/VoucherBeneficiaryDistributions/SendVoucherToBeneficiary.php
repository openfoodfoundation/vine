<?php

namespace App\Jobs\VoucherBeneficiaryDistributions;

use App\Models\VoucherBeneficiaryDistribution;
use App\Notifications\VoucherBeneficiaryDistributions\BeneficiaryVoucherDistributionEmail;
use Crypt;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Notification;

class SendVoucherToBeneficiary implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param VoucherBeneficiaryDistribution $voucherBeneficiaryDistribution
     */
    public function __construct(public VoucherBeneficiaryDistribution $voucherBeneficiaryDistribution) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (env('APP_ENV') != 'testing') {

            $address = Crypt::decrypt($this->voucherBeneficiaryDistribution->beneficiary_email_encrypted);

            Notification::route('mail', $address)
                ->notify(new BeneficiaryVoucherDistributionEmail($this->voucherBeneficiaryDistribution));

            $this->voucherBeneficiaryDistribution->email_sent_at = now();
            $this->voucherBeneficiaryDistribution->saveQuietly();
        }
    }
}
