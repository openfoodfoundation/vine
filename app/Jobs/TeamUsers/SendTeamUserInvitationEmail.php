<?php

namespace App\Jobs\TeamUsers;

use App\Models\TeamUser;
use App\Models\User;
use App\Notifications\Mail\TeamUsers\SendTeamUserInvitationEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendTeamUserInvitationEmail implements ShouldQueue
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
        $userToNotify = User::find($this->teamUser->user_id);

        if ($userToNotify) {
            $userToNotify->current_team_id = $this->teamUser->team_id;
            $userToNotify->saveQuietly();

            $userToNotify->notify(new SendTeamUserInvitationEmailNotification($this->teamUser));
        }
    }
}
