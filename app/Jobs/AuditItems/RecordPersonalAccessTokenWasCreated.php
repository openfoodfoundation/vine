<?php

namespace App\Jobs\AuditItems;

use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Services\AuditItemService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class RecordPersonalAccessTokenWasCreated implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param PersonalAccessToken|SanctumPersonalAccessToken $personalAccessToken
     */
    public function __construct(public PersonalAccessToken|SanctumPersonalAccessToken $personalAccessToken) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = User::find($this->personalAccessToken->tokenable_id);

        AuditItemService::createAuditItemForEvent(
            model    : $this->personalAccessToken,
            eventText: 'Access token "' . $this->personalAccessToken->name . '" was created for user ' . $user->name . '.',
            teamId   : $user->current_team_id,
        );
    }
}
