<?php

namespace App\Events\VoucherSetMerchantTeamApprovalRequest;

<<<<<<< HEAD:app/Events/VoucherSetMerchantTeamApprovalRequest/VoucherSetMerchantTeamApprovalRequestWasRejected.php
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Broadcasting\Channel;
=======
>>>>>>> 5552fcbaa21be484b14835406949704debd2367a:app/Events/VoucherSetMerchantTeamApprovalRequestWasRejected.php
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoucherSetMerchantTeamApprovalRequestWasRejected
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param VoucherSetMerchantTeamApprovalRequest $request
     */
<<<<<<< HEAD:app/Events/VoucherSetMerchantTeamApprovalRequest/VoucherSetMerchantTeamApprovalRequestWasRejected.php
    public function __construct(public VoucherSetMerchantTeamApprovalRequest $request) {}
=======
    public function __construct() {}
>>>>>>> 5552fcbaa21be484b14835406949704debd2367a:app/Events/VoucherSetMerchantTeamApprovalRequestWasRejected.php

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
