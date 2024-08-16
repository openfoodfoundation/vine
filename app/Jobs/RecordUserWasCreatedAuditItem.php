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
    public function __construct(public User $createdUser)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        AuditItemService::createAuditItemForEvent(
            model    : $this->createdUser,
            eventText: 'User ' . $this->createdUser->name . ' was created.',
            teamId   : $this->createdUser->current_team_id
        );
    }
}
