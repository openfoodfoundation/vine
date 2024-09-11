<?php

namespace Tests\Feature\API\App\VoucherValidation;

use App\Models\Voucher;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherValidationPostTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/voucher-validation';

    #[Test]
    public function itFailsIfVoucherIdentifierIsWrong()
    {

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet         = VoucherSet::factory()->createQuietly();
        $voucherSetMerchant = VoucherSetMerchantTeam::factory()->createQuietly(
            [
                'voucher_set_id'   => $voucherSet->id,
                'merchant_team_id' => $this->user->current_team_id,
            ]
        );

        $payload = [
            'type'  => 'voucher_id',
            'value' => 'INCORRECT VALUE',
        ];

        $data = json_encode($payload);

        $response = $this->postJson($this->apiRoot . $this->endPoint, $payload);

        $response->assertStatus(404);
    }

    #[Test]
    public function itCanValidateVoucherUsingId()
    {

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet         = VoucherSet::factory()->createQuietly();
        $voucherSetMerchant = VoucherSetMerchantTeam::factory()->createQuietly(
            [
                'voucher_set_id'   => $voucherSet->id,
                'merchant_team_id' => $this->user->current_team_id,
            ]
        );

        $voucher = Voucher::factory()->create(
            [
                'voucher_set_id' => $voucherSet->id,
            ]
        );

        $voucher->refresh();

        $payload = [
            'type'  => 'voucher_id',
            'value' => $voucher->id,
        ];

        $response = $this->postJson($this->apiRoot . $this->endPoint, $payload);
        $response->assertOk();

        $responseObj = json_decode($response->getContent());

        $this->assertEquals($voucher->id, $responseObj->data->id);
    }

    #[Test]
    public function itCanValidateVoucherUsingShortCode()
    {

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet         = VoucherSet::factory()->createQuietly();

        $voucherSetMerchant = VoucherSetMerchantTeam::factory()->createQuietly(
            [
                'voucher_set_id'   => $voucherSet->id,
                'merchant_team_id' => $this->user->current_team_id,
            ]
        );

        $voucher = Voucher::factory()->create(
            [
                'voucher_set_id' => $voucherSet->id,
            ]
        );

        $voucher->refresh();

        $payload = [
            'type'  => 'voucher_code',
            'value' => $voucher->voucher_short_code,
        ];

        $response = $this->postJson($this->apiRoot . $this->endPoint, $payload);

        $responseObj = json_decode($response->getContent());

        $response->assertOk();
        $this->assertEquals($voucher->id, $responseObj->data->id);
    }

    #[Test]
    public function itHidesSensitiveFieldsFromValidationResponse()
    {

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet         = VoucherSet::factory()->createQuietly();

        $voucherSetMerchant = VoucherSetMerchantTeam::factory()->createQuietly(
            [
                'voucher_set_id'   => $voucherSet->id,
                'merchant_team_id' => $this->user->current_team_id,
            ]
        );

        $voucher = Voucher::factory()->create(
            [
                'voucher_set_id' => $voucherSet->id,
            ]
        );

        $voucher->refresh();

        $payload = [
            'type'  => 'voucher_code',
            'value' => $voucher->voucher_short_code,
        ];

        $response = $this->postJson($this->apiRoot . $this->endPoint, $payload);

        $responseObj = json_decode($response->getContent());

        $response->assertOk();
        $this->assertEquals($voucher->id, $responseObj->data->id);
        $this->assertObjectNotHasProperty(propertyName: 'created_by_team_id', object: $responseObj->data);
        $this->assertObjectNotHasProperty(propertyName: 'allocated_to_service_team_id', object: $responseObj->data);
    }
}
