<?php

namespace App\Listeners\VoucherBeneficiaryDistributions;

use App\Events\VoucherBeneficiaryDistributions\VoucherBeneficiaryDistributionCreated;
use App\Jobs\VoucherBeneficiaryDistributions\SendVoucherToBeneficiary;

class HandleVoucherBeneficiaryDistributionCreatedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param VoucherBeneficiaryDistributionCreated $event
     */
    public function handle(VoucherBeneficiaryDistributionCreated $event): void
    {
        dispatch(new SendVoucherToBeneficiary($event->voucherBeneficiaryDistribution));
    }
}
