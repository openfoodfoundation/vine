<?php

namespace App\Notifications\Mail\TeamUsers;

use App\Models\Team;
use App\Models\TeamUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendTeamUserInvitationEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public TeamUser $teamUser)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $team = Team::find($this->teamUser->team_id);

        return (new MailMessage)
                    ->subject('You have been invited to join ' . $team->name. ' - '.config('app.name'))
                    ->line('You have been invited to join team "' . $team->name. '" on '.config('app.name').'.')
                    ->line('An account has been created for you on the team, but if you have never logged in to use the system, you may need to reset your password in order to log in.')
                    ->line('Please follow the button below to reset your password and log in.')
                    ->action('Reset your password & log in', url('/forgot-password'))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
