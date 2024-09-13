<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use App\Notifications\Mail\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestEmailNotification;
use Illuminate\Console\Command;

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

        //        $test = VoucherSetMerchantTeamApprovalRequest::factory()->create([
        //            'voucher_set_id'=> '2a8b36c2-cc05-3726-ba93-f4bf452e885a',
        //            'merchant_user_id' => 2
        //        ]);

        $request = VoucherSetMerchantTeamApprovalRequest::find(1);

        $me->notify(new VoucherSetMerchantTeamApprovalRequestEmailNotification($request));

    }
}
