<?php

namespace App\Jobs\Vouchers;

use App\Models\Voucher;
use App\Models\VoucherTemplate;
use App\Services\VoucherTemplateService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateStorageVoucherQrCode implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param Voucher $voucher
     */
    public function __construct(public Voucher $voucher) {}

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        $redeemUrl = URL::to('/redeem/' . $this->voucher->voucher_set_id . '/' . $this->voucher->id);

        /**
         * PNG
         */
        $dataPng = QrCode::format('png')->size(600)->generate($redeemUrl);
        $path    = '/voucher-sets/' . $this->voucher->voucher_set_id . '/vouchers/individual/' . $this->voucher->id . '/png/voucher-qr.png';
        $file    = Storage::put($path, $dataPng);

        $path = '/voucher-sets/' . $this->voucher->voucher_set_id . '/vouchers/all/png/' . $this->voucher->id . '.png';
        $file = Storage::put($path, $dataPng);

        /**
         * SVG
         */
        $dataSvg = QrCode::format('svg')->size(600)->generate($redeemUrl);
        $path    = '/voucher-sets/' . $this->voucher->voucher_set_id . '/vouchers/individual/' . $this->voucher->id . '/svg/voucher-qr.svg';
        $file    = Storage::put($path, $dataSvg);

        $path = '/voucher-sets/' . $this->voucher->voucher_set_id . '/vouchers/all/svg/' . $this->voucher->id . '.svg';
        $file = Storage::put($path, $dataSvg);

        // Get template for the team - this may not be needed as users select template from frontend
        //        $voucherTemplate = VoucherTemplate::where('team_id', $this->voucher->created_by_team_id)->first();
        //
        //        if ($voucherTemplate) {
        //            VoucherTemplateService::generateVoucherTemplate($voucherTemplate, $this->voucher);
        //        }
    }
}
