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
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $payload = [];

        $response = $this->getJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotAccess()
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
    public function itCanGetAllResources()
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
    public function itCanNotGetAllResourcesIncorrectAbility()
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
    public function itCanGetASingleResource()
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
