<?php

namespace App\Listeners\Users;

use App\Events\Users\UserWasCreated;
use App\Jobs\RecordUserWasCreatedAuditItem;
use Auth;

class HandleUserWasCreatedEvent
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param UserWasCreated $event
     */
    public function handle(UserWasCreated $event): void
    {
        if (env('APP_ENV') != 'testing') {
            dispatch(
                new RecordUserWasCreatedAuditItem(
                    actioningUser: Auth::user(),
                    createdUser: $event->user
                )
            );
        }
    }
}
