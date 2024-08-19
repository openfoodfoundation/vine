<?php

namespace App\Jobs\AuditItems;

use App\Models\Team;
use App\Models\User;
use App\Services\AuditItemService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordTeamWasCreatedAuditItem implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param Team $team
     */
    public function __construct(public Team $team)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        AuditItemService::createAuditItemForEvent(
            model    : $this->team,
            eventText: 'Team ' . $this->team->name . ' was created.',
            teamId   : $this->team->id
        );
    }
}
