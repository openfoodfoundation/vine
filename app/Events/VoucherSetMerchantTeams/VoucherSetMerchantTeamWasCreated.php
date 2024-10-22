<?php

namespace App\Events\VoucherSetMerchantTeams;

use App\Models\VoucherSetMerchantTeam;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoucherSetMerchantTeamWasCreated
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param VoucherSetMerchantTeam $voucherSetMerchantTeam
     */
    public function __construct(public VoucherSetMerchantTeam $voucherSetMerchantTeam) {}

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