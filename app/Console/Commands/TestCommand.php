<?php

namespace App\Console\Commands;

use App\Models\User;
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

        $me = User::find(1);

        $approvalRequest = VoucherSetMerchantTeamApprovalRequest::factory()->create([
            'voucher_set_id'   => 'e6765b13-0d7a-3c8b-870a-ca8765c27b35',
            'merchant_team_id' => 1,
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
