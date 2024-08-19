<?php

namespace App\Listeners\Teams;

use App\Events\Teams\TeamWasCreated;
use App\Jobs\AuditItems\RecordTeamWasCreatedAuditItem;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleTeamWasCreatedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param TeamWasCreated $event
     */
    public function handle(TeamWasCreated $event): void
    {
        dispatch(new RecordTeamWasCreatedAuditItem($event->team));
    }
}
