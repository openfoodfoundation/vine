<?php

namespace App\Listeners\Users;

use App\Events\Users\UserWasCreated;
use App\Jobs\AuditItems\RecordUserWasCreatedAuditItem;

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

        dispatch(
            new RecordUserWasCreatedAuditItem(
                createdUser: $event->user
            )
        );

    }
}
