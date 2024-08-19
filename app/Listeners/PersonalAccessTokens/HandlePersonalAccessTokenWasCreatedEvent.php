<?php

namespace App\Listeners\PersonalAccessTokens;

use App\Events\PersonalAccessTokens\PersonalAccessTokenWasCreated;
use App\Jobs\AuditItems\RecordPersonalAccessTokenWasCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class HandlePersonalAccessTokenWasCreatedEvent implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PersonalAccessTokenWasCreated $event): void
    {
        dispatch(new RecordPersonalAccessTokenWasCreated($event->personalAccessToken));
    }
}
