<?php

namespace Tests\Feature\API\App\SystemStatistics;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class SystemStatisticPostTest extends BaseAPITestCase
{
    //    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/system-statistics';

    #[Test]
    public function authentication_required(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);
        $payload = [];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_cannot_store_a_resource_method_not_allowed()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SYSTEM_STATISTICS_CREATE->value,
        ]);

        $payload = [];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(403)
            ->assertJson(
                [
                    'meta' => [
                        'message' => ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value,
                    ],
                ]
            );
    }

    #[Test]
    public function it_cannot_store_a_resource_incorrect_token_abilities()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $payload = [];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401)
            ->assertJson(
                [
                    'meta' => [
                        'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                    ],
                ]
            );
    }
}
