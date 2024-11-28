<?php

namespace App\Events\VoucherBeneficiaryDistributions;

use App\Models\VoucherBeneficiaryDistribution;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoucherBeneficiaryDistributionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param VoucherBeneficiaryDistribution $voucherBeneficiaryDistribution
     */
    public function __construct(public VoucherBeneficiaryDistribution $voucherBeneficiaryDistribution) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
