<?php

namespace App\Console\Commands\VoucherSets;

use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use App\Notifications\Mail\VoucherSets\VoucherSetGenerationSuccessEmailNotification;
use App\Notifications\Slack\VoucherSets\VoucherSetGenerationFailedNotification;
use App\Services\VoucherSetService;
use Illuminate\Console\Command;

class GenerateVoucherSetVouchersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:voucher-set:vouchers:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates vouchers for a newly created voucher set';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $voucherSet = VoucherSet::where('is_denomination_valid', 1)->whereNull('voucher_generation_started_at')->whereNull('voucher_generation_finished_at')->first();

        if ($voucherSet) {
            $voucherSet->voucher_generation_started_at = now();
            $voucherSet->save();

            /**
             * Re-validate the denomination JSON
             */
            $voucherSetIsValid = VoucherSetService::validateVoucherSetDenominations(voucherSet: $voucherSet);

            if (!$voucherSetIsValid) {

                $this->line('Voucher set is invalid.');

                $user = User::first();
                $user->notify(new VoucherSetGenerationFailedNotification(voucherSet: $voucherSet, reason: 'The voucher set denomination was invalid.'));

                return 0;
            }

            /**
             * voucher set denomination is valid
             */
            $denominationArray = json_decode($voucherSet->denomination_json, true);
            $numCreated        = 0;
            $numToBeCreated    = 0;
            foreach ($denominationArray as $denominationListing) {

                $numToBeCreated = $numToBeCreated + $denominationListing['number'];

                for ($i = 1; $i <= $denominationListing['number']; $i++) {
                    $model                               = new Voucher();
                    $model->voucher_set_id               = $voucherSet->id;
                    $model->created_by_team_id           = $voucherSet->created_by_team_id;
                    $model->allocated_to_service_team_id = $voucherSet->allocated_to_service_team_id;
                    $model->voucher_value_original       = $denominationListing['value'];
                    $model->voucher_value_remaining      = $denominationListing['value'];
                    $model->is_test                      = $voucherSet->is_test;
                    $model->save();

                    $numCreated++;

                    if (($numCreated % $numToBeCreated) == 0) {
                        $this->line($numCreated . ' vouchers generated.');
                    }
                }
            }

            $voucherSet->voucher_generation_finished_at = now();
            $voucherSet->save();

            $notificationUser = User::find($voucherSet->created_by_user_id);

            $notificationUser?->notify(new VoucherSetGenerationSuccessEmailNotification(voucherSet: $voucherSet));

        }

        return 0;

    }
}
