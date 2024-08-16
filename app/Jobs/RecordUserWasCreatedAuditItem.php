<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\AuditItemService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordUserWasCreatedAuditItem implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param User $actioningUser
     * @param User $createdUser
     */
    public function __construct(public User $actioningUser, public User $createdUser) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $eventText = '';

        if ($this->actioningUser->is_admin) {
            $eventText .= 'Admin ';
        }

        $eventText .= $this->actioningUser->name . ' created a new user ' . $this->createdUser->name . '.';

        AuditItemService::createAuditItemForEvent(
            actioningUser: $this->actioningUser,
            model: $this->createdUser,
            eventText: $eventText
        );
    }
}
