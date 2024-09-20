<?php

namespace App\Notifications\Slack\VoucherSets;

use App\Models\VoucherSet;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ActionsBlock;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ContextBlock;
use Illuminate\Notifications\Slack\BlockKit\Blocks\SectionBlock;
use Illuminate\Notifications\Slack\SlackMessage;
use Illuminate\Support\Facades\URL;

class VoucherSetGenerationFailedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param VoucherSet $voucherSet
     * @param string     $reason
     */
    public function __construct(public VoucherSet $voucherSet, public string $reason) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param object $notifiable
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     *
     * @return SlackMessage
     */
    public function toSlack(object $notifiable): SlackMessage
    {
        $link = URL::to('/voucher-set/' . $this->voucherSet);

        return (new SlackMessage())
            ->headerBlock(':no_good: ' . $this->reason)
            ->contextBlock(function (ContextBlock $block) {
                $block->text('Voucher set #' . $this->voucherSet . ' was not generated.');
            })
            ->sectionBlock(function (SectionBlock $section) {
                $section->text('Reason: ' . $this->reason);
            })
            ->actionsBlock(function (ActionsBlock $action) use ($link) {
                $action->button('Go to voucher set page')->url($link);
            });
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
