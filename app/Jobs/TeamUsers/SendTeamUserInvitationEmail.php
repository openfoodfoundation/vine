<?php

namespace App\Jobs\TeamUsers;

use App\Models\TeamUser;
use App\Models\User;
use App\Notifications\Mail\TeamUsers\SendTeamUserInvitationEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendTeamUserInvitationEmail implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public TeamUser $teamUser)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $userToNotify = User::find($this->teamUser->user_id);

        if($userToNotify)
        {
            $userToNotify->current_team_id = $this->teamUser->team_id;
            $userToNotify->saveQuietly();

            $userToNotify->notify(new SendTeamUserInvitationEmailNotification($this->teamUser));
        }
    }
}
