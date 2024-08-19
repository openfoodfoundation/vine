<?php

namespace App\Jobs\AuditItems;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\Services\AuditItemService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordTeamUserWasCreatedAuditItem implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param TeamUser $teamUser
     */
    public function __construct(public TeamUser $teamUser) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->teamUser->user_id);
        $team = Team::find($this->teamUser->team_id);

        AuditItemService::createAuditItemForEvent(
            model    : $this->teamUser,
            eventText: 'User ' . $user->name . ' was added to team "' . $team->name . '".',
            teamId   : $this->teamUser->team_id
        );
    }
}
