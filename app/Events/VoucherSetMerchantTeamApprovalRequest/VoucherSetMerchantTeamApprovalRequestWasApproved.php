<?php

namespace App\Events\VoucherSetMerchantTeamApprovalRequest;

<<<<<<< HEAD:app/Events/VoucherSetMerchantTeamApprovalRequest/VoucherSetMerchantTeamApprovalRequestWasApproved.php
use App\Models\VoucherSetMerchantTeamApprovalRequest;
=======
>>>>>>> 5552fcbaa21be484b14835406949704debd2367a:app/Events/VoucherSetMerchantTeamApprovalRequestWasApproved.php
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
<<<<<<< HEAD:app/Events/VoucherSetMerchantTeamApprovalRequest/VoucherSetMerchantTeamApprovalRequestWasApproved.php
    public function __construct(public VoucherSetMerchantTeamApprovalRequest $request) {}
=======
    public function __construct() {}
>>>>>>> 5552fcbaa21be484b14835406949704debd2367a:app/Events/VoucherSetMerchantTeamApprovalRequestWasApproved.php

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
