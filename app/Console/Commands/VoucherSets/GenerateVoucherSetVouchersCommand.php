<?php

namespace App\Console\Commands\VoucherSets;

use App\Events\VoucherSets\VoucherSetWasGenerated;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use App\Notifications\Slack\VoucherSets\VoucherSetGenerationFailedNotification;
use App\Services\VoucherService;
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
        $voucherSet = VoucherSet::where('is_denomination_valid', 1)
            ->whereNull('voucher_generation_started_at')
            ->whereNull('voucher_generation_finished_at')
            ->whereNotNull('merchant_approval_request_id')
            ->first();

        if ($voucherSet) {
            $voucherSet->voucher_generation_started_at = now();
            $voucherSet->saveQuietly();

            VoucherSetService::collateVoucherSetAggregates(voucherSet: $voucherSet);
            $voucherSet->refresh();

            if (!$voucherSet->is_denomination_valid) {

                $this->line('Voucher set is invalid.');

                $user = User::first();
                $user->notify(new VoucherSetGenerationFailedNotification(voucherSet: $voucherSet, reason: 'The voucher set denomination is invalid. Please investigate.'));

                /**
                 * Re-set this voucher set
                 */
                $voucherSet->voucher_generation_started_at  = null;
                $voucherSet->voucher_generation_finished_at = null;
                $voucherSet->saveQuietly();

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
                    $model->voucher_short_code           = VoucherService::findUniqueShortCodeForVoucher();
                    $model->save();

                    $numCreated++;

                    if (($numCreated % 100) == 0) {
                        $this->line($numCreated . ' vouchers generated.');
                    }
                }
            }

            $voucherSet->voucher_generation_finished_at = now();
            $voucherSet->save();

            event(new VoucherSetWasGenerated($voucherSet));

        }

        return 0;

    }
}
