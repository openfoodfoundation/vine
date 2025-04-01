<?php

namespace Tests\Feature\API\App\SystemStatistics;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Models\SystemStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class SystemStatisticsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/system-statistics';

    #[Test]
    public function authentication_required(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $payload = [];

        $response = $this->getJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function standard_user_without_permission_cannot_access()
    {
        $this->user = $this->createUser();

        SystemStatistic::factory()->create();

        Sanctum::actingAs($this->user, abilities: []);

        $payload = [];

        $response = $this->getJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );

    }

    #[Test]
    public function it_can_get_all_resources()
    {
        $this->user = $this->createUser();

        SystemStatistic::factory()->create();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SYSTEM_STATISTICS_READ->value,
        ]);

        $payload = [];

        $response = $this->getJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(200);

    }

    #[Test]
    public function it_can_not_get_all_resources_incorrect_ability()
    {
        $this->user = $this->createUser();

        SystemStatistic::factory()->create();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SYSTEM_STATISTICS_UPDATE->value,
        ]);

        $payload = [];

        $response = $this->getJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );

    }

    #[Test]
    public function it_can_get_a_single_resource()
    {
        $this->user = $this->createUser();

        $model = SystemStatistic::factory()->create();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SYSTEM_STATISTICS_READ->value,
        ]);

        $payload = [];

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(200);
    }
}
