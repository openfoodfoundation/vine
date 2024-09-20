<?php

namespace App\Events\VoucherSetMerchantTeamApprovalRequest;

use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoucherSetMerchantTeamApprovalRequestWasApproved
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param VoucherSetMerchantTeamApprovalRequest $voucherSetMerchantTeamApprovalRequest
     */
    public function __construct(public VoucherSetMerchantTeamApprovalRequest $voucherSetMerchantTeamApprovalRequest) {}

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
