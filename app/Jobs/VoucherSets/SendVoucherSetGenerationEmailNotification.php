<?php

/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection PhpUndefinedMethodInspection */

namespace App\Jobs\VoucherSets;

use App\Models\User;
use App\Models\VoucherSet;
use App\Notifications\Mail\VoucherSets\VoucherSetGenerationEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendVoucherSetGenerationEmailNotification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param VoucherSet $voucherSet
     */
    public function __construct(public VoucherSet $voucherSet) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $serviceUsers = User::where('current_team_id', $this->voucherSet->allocated_to_service_team_id)->get();

        if ($serviceUsers) {
            foreach ($serviceUsers as $user) {
                $user->notify(new VoucherSetGenerationEmailNotification($this->voucherSet));
            }
        }

    }
}
