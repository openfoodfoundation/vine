<?php

namespace App\Providers;

use App\Events\Users\UserWasCreated;
use App\Listeners\Users\HandleUserWasCreatedEvent;
use Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(
            events: UserWasCreated::class,
            listener: HandleUserWasCreatedEvent::class
        );
    }
}
