<?php

namespace App\Listeners\TeamUsers;

use App\Events\TeamUsers\TeamUserWasCreated;
use App\Jobs\AuditItems\RecordTeamUserWasCreatedAuditItem;

class HandleTeamUserWasCreatedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param TeamUserWasCreated $event
     */
    public function handle(TeamUserWasCreated $event): void
    {
        dispatch(new RecordTeamUserWasCreatedAuditItem($event->teamUser));
    }
}
