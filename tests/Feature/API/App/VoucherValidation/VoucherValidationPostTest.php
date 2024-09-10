<?php

namespace Tests\Feature\API\App\VoucherValidation;

use App\Jobs\Vouchers\AssignUniqueShortCodeToVoucherJob;
use App\Models\Voucher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherValidationPostTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/voucher-validation';

    #[Test]
    public function itFailsIfSignatureIsIncorrect()
    {
        $voucher = Voucher::factory()->createQuietly();

        $payload = [
            'type'  => 'voucher_id',
            'value' => $voucher->id,
        ];

        $data = json_encode($payload);

        $verificationSignature = hash_hmac('sha256', $data, 'Incorrect Secret');

        $response = $this->withHeader('X-HMAC-Signature', $verificationSignature)
            ->postJson($this->apiRoot . $this->endPoint, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function itFailsIfVoucherIdentifierIsWrong()
    {
        $payload = [
            'type'  => 'voucher_id',
            'value' => 'INCORRECT VALUE',
        ];

        $data = json_encode($payload);

        $verificationSignature = hash_hmac('sha256', $data, 'Secret');

        $response = $this->withHeader('X-HMAC-Signature', $verificationSignature)
            ->postJson($this->apiRoot . $this->endPoint, $payload);

        $response->assertStatus(400);
    }


    #[Test]
    public function itCanValidateVoucherUsingShortCode()
    {
        $voucher = Voucher::factory()->create();
        dispatch(new AssignUniqueShortCodeToVoucherJob($voucher));
        $voucher->refresh();

        $payload = [
            'type'  => 'voucher_code',
            'value' => $voucher->voucher_short_code,
        ];

        $data = json_encode($payload);

        $verificationSignature = hash_hmac('sha256', $data, 'Secret');

        $response = $this->withHeader('X-HMAC-Signature', $verificationSignature)
            ->postJson($this->apiRoot . $this->endPoint, $payload);

        $response->assertOk();

        $responseObj = json_decode($response->getContent());

        $this->assertEquals($voucher->id, $responseObj->data->id);
    }

    #[Test]
    public function itCanValidateVoucherUsingId()
    {
        $voucher = Voucher::factory()->createQuietly();

        $payload = [
            'type'  => 'voucher_id',
            'value' => $voucher->id,
        ];

        $data = json_encode($payload);

        $verificationSignature = hash_hmac('sha256', $data, 'Secret');

        $response = $this->withHeader('X-HMAC-Signature', $verificationSignature)
            ->postJson($this->apiRoot . $this->endPoint, $payload);

        $response->assertOk();

        $responseObj = json_decode($response->getContent());

        $this->assertEquals($voucher->id, $responseObj->data->id);
    }
}
