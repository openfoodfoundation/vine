<?php

namespace App\Console\Commands;


use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use Illuminate\Console\Command;
use stdClass;
use App\Models\Team;
use App\Models\TeamMerchantTeam;


class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test command. Do not run in production.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $teams = Team::factory(8)->createQuietly();


        //        $voucher = new Voucher();
        //        $voucher->voucher_set_id = 'abc123';
        //        $voucher->created_by_team_id = 1;
        //        $voucher->allocated_to_service_team_id = 2;
        //        $voucher->voucher_value_original = 0;
        //        $voucher->voucher_value_remaining = 0;
        //        $voucher->num_voucher_redemptions = 0;
        //        $voucher->save();

        //        $voucher = Voucher::factory()->create();

        $me = User::find(2);

        $denomination = [];

        $voucherItem              = new stdClass();
        $voucherItem->value       = 100;
        $voucherItem->number      = 5;
        $voucherItem->dollarValue = 5;
        $denomination[]           = $voucherItem;

        $voucherItem              = new stdClass();
        $voucherItem->value       = 500;
        $voucherItem->number      = 2;
        $voucherItem->dollarValue = 10;
        $denomination[]           = $voucherItem;

        $voucherItem              = new stdClass();
        $voucherItem->value       = 1000;
        $voucherItem->number      = 2;
        $voucherItem->dollarValue = 20;
        $denomination[]           = $voucherItem;

        $myJSON = json_encode($denomination);

        $voucherSet = VoucherSet::factory()->create([
            'created_by_team_id'           => 1,
            'allocated_to_service_team_id' => 2,
            'created_by_user_id'           => $me->id,
            'total_set_value'              => 3500,
            'total_set_value_remaining'    => 3500,
            'num_vouchers'                 => 0,
            'num_voucher_redemptions'      => 0,
            'denomination_json'            => $myJSON,
            'is_denomination_valid'        => 1,
        ]);


        foreach ($teams as $team) {

            TeamMerchantTeam::factory()->createQuietly(
                [
                    'merchant_team_id' => $team->id,
                    'team_id'          => 1,
                ]
            );
        }

    }
}
