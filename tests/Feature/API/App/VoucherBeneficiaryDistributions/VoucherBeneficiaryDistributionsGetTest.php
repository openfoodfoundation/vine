<?php

namespace Tests\Feature\API\App\VoucherBeneficiaryDistributions;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherBeneficiaryDistributionsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/voucher-beneficiary-distributions';

    #[Test]
    public function authentication_required(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function standard_user_without_permission_cannot_access()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );

    }

    #[Test]
    public function it_can_not_get_all_resources()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(403);

    }

    #[Test]
    public function it_can_not_get_a_single_resource()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(403);
    }
}
