<?php

namespace Tests\Unit\Services;

use App\Models\Voucher;
use App\Models\VoucherSet;
use App\Models\VoucherTemplate;
use App\Services\VoucherTemplateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Laravel\Facades\Image;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VoucherTemplateServiceTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itGeneratesVoucherTemplateForVoucher(): void
    {
        Storage::fake();

        $templateImagePath = 'teams/1/voucher-templates/template.png';

        // Create a real 100x100 PNG image for the template
        $templateImage = Image::createImage(100, 100);
        Storage::put($templateImagePath, (string) $templateImage->encode(new PngEncoder()));

        $voucherTemplate = VoucherTemplate::factory()->create([
            'voucher_template_path'         => $templateImagePath,
            'voucher_example_template_path' => $templateImagePath . '.example.png',
            'overlay_font_path'             => 'fonts/Roboto-Regular.ttf',
            'voucher_qr_size_px'            => 50,
            'voucher_qr_x'                  => 10,
            'voucher_qr_y'                  => 10,
            'voucher_code_size_px'          => 16,
            'voucher_code_x'                => 10,
            'voucher_code_y'                => 70,
            'voucher_code_prefix'           => 'Code:',
            'voucher_expiry_size_px'        => 16,
            'voucher_expiry_x'              => 10,
            'voucher_expiry_y'              => 80,
            'voucher_expiry_prefix'         => 'Expiry:',
            'voucher_value_size_px'         => 16,
            'voucher_value_x'              => 10,
            'voucher_value_y'              => 90,
            'voucher_value_prefix'          => '$',
        ]);

        $voucherSet = VoucherSet::factory()->create([
            'voucher_template_id' => $voucherTemplate->id,
            'expires_at'          => now()->addYear(),
        ]);

        $voucher = Voucher::factory()->create([
            'voucher_set_id'         => $voucherSet->id,
            'voucher_short_code'     => 'AB1234',
            'voucher_value_original' => 5000,
        ]);

        // Create a QR code PNG in the expected storage path
        $qrImage = Image::createImage(100, 100);
        $qrPath = 'voucher-sets/' . $voucherSet->id . '/vouchers/all/png/' . $voucher->id . '.png';
        Storage::put($qrPath, (string) $qrImage->encode(new PngEncoder()));

        VoucherTemplateService::generateVoucherTemplate(
            voucherTemplate: $voucherTemplate,
            voucher: $voucher,
        );

        // Verify branded files were created
        $brandedAllPath = '/voucher-sets/' . $voucherSet->id . '/vouchers/all/branded/' . $voucher->id . '.png';
        $brandedIndividualPath = '/voucher-sets/' . $voucherSet->id . '/vouchers/individual/' . $voucher->id . '/branded/voucher-branded.png';

        Storage::assertExists($brandedAllPath);
        Storage::assertExists($brandedIndividualPath);
    }

    #[Test]
    public function itGeneratesWorkingVoucherTemplate(): void
    {
        Storage::fake();

        $templateImagePath = 'teams/1/voucher-templates/template.png';

        $templateImage = Image::createImage(100, 100);
        Storage::put($templateImagePath, (string) $templateImage->encode(new PngEncoder()));

        $voucherTemplate = VoucherTemplate::factory()->create([
            'voucher_template_path'         => $templateImagePath,
            'voucher_example_template_path' => $templateImagePath . '.example.png',
            'overlay_font_path'             => 'fonts/Roboto-Regular.ttf',
            'voucher_qr_size_px'            => 50,
            'voucher_qr_x'                  => 10,
            'voucher_qr_y'                  => 10,
            'voucher_code_size_px'          => 16,
            'voucher_code_x'                => 10,
            'voucher_code_y'                => 70,
            'voucher_code_prefix'           => 'Code:',
            'voucher_expiry_size_px'        => 16,
            'voucher_expiry_x'              => 10,
            'voucher_expiry_y'              => 80,
            'voucher_expiry_prefix'         => 'Expiry:',
            'voucher_value_size_px'         => 16,
            'voucher_value_x'              => 10,
            'voucher_value_y'              => 90,
            'voucher_value_prefix'          => '$',
        ]);

        VoucherTemplateService::generateWorkingVoucherTemplate(
            voucherTemplate: $voucherTemplate,
        );

        Storage::assertExists($voucherTemplate->voucher_example_template_path);
    }
}
