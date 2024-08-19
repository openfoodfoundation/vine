<?php

namespace App\Listeners\PersonalAccessTokens;

use App\Events\PersonalAccessTokens\PersonalAccessTokenWasCreated;
use App\Jobs\AuditItems\RecordPersonalAccessTokenWasCreated;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandlePersonalAccessTokenWasCreatedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param PersonalAccessTokenWasCreated $event
     */
    public function handle(PersonalAccessTokenWasCreated $event): void
    {
        dispatch(new RecordPersonalAccessTokenWasCreated($event->personalAccessToken));
    }
}
