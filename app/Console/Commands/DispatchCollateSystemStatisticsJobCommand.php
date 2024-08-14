<?php

namespace App\Console\Commands;

use App\Models\SystemStatistic;
use App\Models\Team;
use App\Models\User;
use Illuminate\Console\Command;

class DispatchCollateSystemStatisticsJobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dispatch-collate-system-statistics-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to dispatch the collate system statistics job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SystemStatistic::create([
            'num_users' => User::count(),
            'num_teams' => Team::count(),
            // TODO when other models are in place
            //                'num_voucher_sets' => VoucherSet::count(),
            //                'num_vouchers' => Voucher::count(),
            //                'num_voucher_redemptions' => VoucherRedemption::count(),
            //                'sum_voucher_value_total' => Voucher::sum('value_total'),
            //                'sum_voucher_value_redeemed' => Voucher::sum('value_redeemed'),
            //                'sum_voucher_value_remaining' => Voucher::sum('value_remaining'),
        ]);
    }
}
