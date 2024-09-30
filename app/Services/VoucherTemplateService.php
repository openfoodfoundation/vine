<?php

/** @noinspection PhpUndefinedFieldInspection */

/** @noinspection DuplicatedCode */

namespace App\Services;

//use App\Jobs\Vouchers\GenerateStorageBrandedPDF;
use App\Models\Voucher;
use App\Models\VoucherSet;
use App\Models\VoucherTemplate;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Imagick;
use ImagickException;
use Intervention\Image\Laravel\Facades\Image;

class VoucherTemplateService
{
    public const BLEED_LINE_AMOUNT_MILLIMETRES = 5;

    public static function generateWorkingVoucherTemplate(VoucherTemplate $voucherTemplate): void
    {
        $img1 = Image::read(public_path('img/example-qr.png')); // The QR

        $img1->resize($voucherTemplate->voucher_qr_size_px, $voucherTemplate->voucher_qr_size_px);

        /**
         * Mount the template for usage
         */
        $img = Storage::get($voucherTemplate->voucher_template_path);

        // open an image file

        $img = Image::read($img);

        // and insert the QR code
        $img->place($img1, 'top-left', $voucherTemplate->voucher_qr_x, $voucherTemplate->voucher_qr_y);

        $code = 'AB1234';

        $img->text($voucherTemplate->voucher_code_prefix . ' ' . $code, $voucherTemplate->voucher_code_x, $voucherTemplate->voucher_code_y, function ($font) use ($voucherTemplate) {
            $font->file(public_path($voucherTemplate->overlay_font_path));
            $font->size($voucherTemplate->voucher_code_size_px);
        });

        $img->text(
            $voucherTemplate->voucher_expiry_prefix . ' 11-Jan-2025',
            $voucherTemplate->voucher_expiry_x,
            $voucherTemplate->voucher_expiry_y,
            function ($font) use ($voucherTemplate) {
                $font->file(public_path($voucherTemplate->overlay_font_path));
                $font->size($voucherTemplate->voucher_expiry_size_px);
            }
        );

        $img->text($voucherTemplate->voucher_value_prefix . '25.99', $voucherTemplate->voucher_value_x, $voucherTemplate->voucher_value_y, function ($font) use ($voucherTemplate) {
            $font->file(public_path($voucherTemplate->overlay_font_path));
            $font->size($voucherTemplate->voucher_value_size_px);
        });

        Storage::put($voucherTemplate->voucher_example_template_path, (string) $img->encode());
    }

    /**
     * @param VoucherTemplate $voucherTemplate
     * @param Voucher         $voucher
     *
     * @throws Exception
     */
    public static function generateVoucherTemplate(VoucherTemplate $voucherTemplate, Voucher $voucher): void
    {
        /**
         * Mount the template for usage
         */
        $img = Storage::get($voucherTemplate->voucher_template_path);

        // open an image file
        $img = Image::read($img);

        $qrCodeData = Storage::get('voucher-sets/' . $voucher->voucher_set_id . '/vouchers/all/png/' . $voucher->id . '.png');

        $qrCode = Image::read($qrCodeData); // The QR
        $qrCode->resize($voucherTemplate->voucher_qr_size_px, $voucherTemplate->voucher_qr_size_px);

        /**
         * Place the QR code
         */
        $img->place($qrCode, 'top-left', $voucherTemplate->voucher_qr_x, $voucherTemplate->voucher_qr_y);

        /**
         * Place the voucher short code
         */
        $code = strtoupper($voucher->voucher_short_code);
        $img->text($voucherTemplate->voucher_code_prefix . ' ' . $code, $voucherTemplate->voucher_code_x, $voucherTemplate->voucher_code_y, function ($font) use ($voucherTemplate) {
            $font->file(public_path($voucherTemplate->overlay_font_path));
            $font->size($voucherTemplate->voucher_code_size_px);
        });

        /**
         * Place the expiry
         */
        $voucherSet = VoucherSet::find($voucher->voucher_set_id);
        $expiry = ($voucherSet->expires_at == null) ? '---' : $voucherSet->expires_at->format('d-M-Y');
        $img->text(
            $voucherTemplate->voucher_expiry_prefix . ' ' . $expiry,
            $voucherTemplate->voucher_expiry_x,
            $voucherTemplate->voucher_expiry_y,
            function ($font) use ($voucherTemplate) {
                $font->file(public_path($voucherTemplate->overlay_font_path));
                $font->size($voucherTemplate->voucher_expiry_size_px);
            }
        );


        $value = number_format(($voucher->voucher_value_original / 100), 2);
        $img->text(
            $voucherTemplate->voucher_value_prefix . $value,
            $voucherTemplate->voucher_value_x,
            $voucherTemplate->voucher_value_y,
            function ($font) use ($voucherTemplate) {
                $font->file(public_path($voucherTemplate->overlay_font_path));
                $font->size($voucherTemplate->voucher_value_size_px);
            }
        );

        // Place the branded file in different locations
        $path = '/voucher-sets/' . $voucher->voucher_set_id . '/vouchers/all/branded/' . $voucher->id . '.png';
        Storage::put($path, (string) $img->encode());

        $path = '/voucher-sets/' . $voucher->voucher_set_id . '/vouchers/individual/' . $voucher->id . '/branded/voucher-branded.png';
        Storage::put($path, (string) $img->encode());

        //        dispatch(new GenerateStorageBrandedPDF($voucher));

    }

    /**
     * Generate a PDF branded card for a given voucher, so long as it has a template
     *
     * @param Voucher $voucher
     *
     * @throws Exception
     */
    public static function generateAndSavePdfFromVoucherImageFile(Voucher $voucher): void
    {
        $localImageFile = self::downloadVoucherImageFileFromS3($voucher);
        if ($localImageFile) {
            $localPdfFile = self::convertLocalImageToPdf($localImageFile);

            self::uploadVoucherPdfToS3($localPdfFile, $voucher);
        }

    }

    /**
     * @param Voucher $voucher
     *
     * @return string
     *
     * @throws Exception
     */
    public static function downloadVoucherImageFileFromS3(Voucher $voucher): string|bool
    {
        $remotePathAllPNG = '/voucher-sets/' . $voucher->voucher_set_id . '/vouchers/all/branded/' . $voucher->id . '.png';

        $exists = Storage::exists($remotePathAllPNG);

        if (!$exists) {
            Log::info('Voucher branded image does not exist for voucher ' . $voucher->id . '. Skipping.');

            return false;
        }

        $fileName = $voucher->id . '.png';
        $fileData = Storage::get($remotePathAllPNG);

        Storage::disk('local')->put($fileName, $fileData);

        return $fileName;
    }

    /**
     * Upload an output file to S3
     *
     * @param string  $fileName
     * @param Voucher $voucher
     *
     * @return bool
     *
     * @throws Exception
     */
    public static function uploadVoucherPdfToS3(string $fileName, Voucher $voucher): bool
    {

        /**
         * Grab the local file
         */
        $exists = Storage::disk('local')->exists($fileName);

        if (!$exists) {
            throw new Exception('Local file not found: ' . $fileName);
        }

        $contents = Storage::disk('local')->get($fileName);

        $remotePathAll = '/voucher-sets/' . $voucher->voucher_set_id . '/vouchers/all/branded/pdf/' . $fileName;

        $remotePathIndividual = '/voucher-sets/' . $voucher->voucher_set_id . '/vouchers/individual/' . $voucher->id . '/branded/pdf/voucher-branded.pdf';

        Storage::put($remotePathAll, $contents);
        Storage::put($remotePathIndividual, $contents);

        if (Storage::exists($remotePathAll) && Storage::exists($remotePathIndividual)) {
            Storage::disk('local')->delete($fileName);
        }

        return Storage::exists($remotePathAll) && Storage::exists($remotePathIndividual);
    }

    /**
     * @param string $fileName
     *
     * @return string
     *
     * @throws ImagickException
     */
    public static function convertLocalImageToPdf(string $fileName): string
    {
        $image = new Imagick(Storage::disk('local')->path($fileName));

        /**
         * Ensure the PNG can be handled in PNG form
         */
        $image->setImageBackgroundColor('white');
        $image = $image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
        $image->setImageColorspace(Imagick::COLORSPACE_SRGB);

        /**
         * Convert it to a PDF
         */
        $image->setImageFormat('pdf');

        /**
         * Save it locally
         */
        $image->writeImage(storage_path('app/' . $fileName . '.pdf'));

        /**
         * Clean up
         */
        $image->clear();
        $image->destroy();

        /**
         * Ensure the PDF is generated
         */
        $exists = Storage::disk('local')->exists($fileName . '.pdf');

        if ($exists) {

            /**
             * Delete the working file
             */
            Storage::disk('local')->delete($fileName);
        }

        return $fileName . '.pdf';
    }
}
