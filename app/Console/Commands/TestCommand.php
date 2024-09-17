<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use App\Notifications\Mail\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestEmailNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;

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

        $me = User::find(2);

        $voucherSet = VoucherSet::factory()->create([
            'created_by_team_id' => 2,
        ]);

        $approvalRequest = VoucherSetMerchantTeamApprovalRequest::factory()->create([
            'voucher_set_id'   => $voucherSet->id,
            'merchant_user_id' => $me->id,
        ]);

        $me->notify(new VoucherSetMerchantTeamApprovalRequestEmailNotification($approvalRequest));

        //        $myUrl = URL::temporarySignedRoute(
        //            'bounce',
        //            now()->addDays(2),
        //            [
        //                'id'           => Crypt::encrypt($me->id),
        //                'redirectPath' => '/my-team',
        //            ]
        //        );
        //
        //        dd($myUrl);

    }
}
