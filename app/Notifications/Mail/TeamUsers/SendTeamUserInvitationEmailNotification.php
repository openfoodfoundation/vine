<?php

namespace App\Notifications\Mail\TeamUsers;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\Services\BounceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendTeamUserInvitationEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param TeamUser $teamUser
     */
    public function __construct(public TeamUser $teamUser) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     */
    public function toMail(object $notifiable): MailMessage
    {
        $team = Team::find($this->teamUser->team_id);

        $urlToVisit = BounceService::generateSignedUrlForUser(
            user        : User::find($notifiable->id),
            expiry      : now()->addDays(2),
            redirectPath: '/dashboard'
        );

        return (new MailMessage())
            ->subject('You have been invited to join ' . $team->name . ' - ' . config('app.name'))
            ->line('You have been invited to join team "' . $team->name . '" on ' . config('app.name') . '.')
            ->line('An account has been created for you on the team, but if you have never logged in to use the system, you will need to set your password in order to log in.')
            ->line('This link will expire after 24 hours.')
            ->line('Please follow the button below.')
            ->action('Set your password & log in', $urlToVisit);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param object $notifiable
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [

        ];
    }
}
