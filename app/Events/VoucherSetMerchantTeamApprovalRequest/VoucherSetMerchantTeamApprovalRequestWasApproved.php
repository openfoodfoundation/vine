<?php

namespace App\Events\VoucherSetMerchantTeamApprovalRequest;

use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoucherSetMerchantTeamApprovalRequestWasApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param VoucherSetMerchantTeamApprovalRequest $request
     */
    public function __construct(public VoucherSetMerchantTeamApprovalRequest $request) {}


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
