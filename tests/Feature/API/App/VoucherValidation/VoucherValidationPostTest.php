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
    public function it_fails_if_voucher_identifier_is_wrong()
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
    public function it_can_validate_voucher_using_id()
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
    public function it_can_validate_voucher_using_short_code()
    {

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->createQuietly();

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
    public function it_hides_sensitive_fields_from_validation_response()
    {

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->createQuietly();

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

    #[Test]
    public function it_allows_requests_based_on_validation_throttle_rules()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->createQuietly();

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
        // Simulate 5 requests
        for ($i = 0; $i < 5; $i++) {
            $response = $this->postJson($this->apiRoot . $this->endPoint, $payload);
            $response->assertStatus(200);
        }
    }

    #[Test]
    public function it_blocks_requests_based_on_validation_throttle_rules()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->createQuietly();

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

        for ($i = 0; $i < config('vine.throttle.validations'); $i++) {
            $response = $this->postJson($this->apiRoot . $this->endPoint, $payload);
            $response->assertStatus(200);
        }

        $extraResponse = $this->postJson($this->apiRoot . $this->endPoint, $payload);

        $extraResponse->assertStatus(429);
    }

    #[Test]
    public function it_blocks_requests_based_on_validation_throttle_rules_but_allows_requests_from_different_users()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->createQuietly();

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

        for ($i = 0; $i < config('vine.throttle.validations'); $i++) {
            $response = $this->postJson($this->apiRoot . $this->endPoint, $payload);
            $response->assertStatus(200);
        }

        $extraResponse = $this->postJson($this->apiRoot . $this->endPoint, $payload);
        $extraResponse->assertStatus(429);

        /**
         * Create a request using a different user / different API token
         */
        $user2 = $this->createUserWithTeam();
        Sanctum::actingAs(
            $user2
        );
        $user2->current_team_id = $this->user->current_team_id;
        $user2->saveQuietly();

        $voucherSetMerchant2 = VoucherSetMerchantTeam::factory()->createQuietly(
            [
                'voucher_set_id'   => $voucherSet->id,
                'merchant_team_id' => $user2->current_team_id, // THe same team
            ]
        );

        $payload2 = [
            'type'  => 'voucher_code',
            'value' => $voucher->voucher_short_code,
        ];

        $extraResponseForNewUser = $this->postJson($this->apiRoot . $this->endPoint, $payload2);
        $extraResponseForNewUser->assertStatus(200);

    }
}
