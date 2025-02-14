<?php

namespace Tests\Feature\API\App\VoucherBeneficiaryDistributions;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherBeneficiaryDistributionsDeleteTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/voucher-beneficiary-distributions';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotDelete()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );

    }

    #[Test]
    public function itCanNotDeleteASingleResource()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_DELETE->value,
        ]);

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(403);
    }
}
