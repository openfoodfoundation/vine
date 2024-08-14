<?php

namespace Tests\Feature\API\App\SystemStatistics;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Models\SystemStatistic;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class SystemStatisticsDeleteTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/system-statistics';

    #[Test]
    public function authenticationRequired()
    {
        $this->user = $this->createUser();

        $tokenString = $this->user->createToken(name: 'Token', abilities: [])->plainTextToken;
        $model       = SystemStatistic::factory()->create();
        $payload     = [];

        $response = $this->withToken($tokenString)
            ->deleteJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotUpdateAResourceIncorrectAbilities()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SYSTEM_STATISTICS_CREATE->value,
        ]);

        $model   = SystemStatistic::factory()->create();
        $payload = [];

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );
    }

    #[Test]
    public function itCannotUpdateAResourceMethodNotAllowed()
    {
        $this->user = $this->createAdminUser();

        $tokenString = $this->user->createToken(
            name     : 'Token',
            abilities: [
                PersonalAccessTokenAbility::SYSTEM_STATISTICS_DELETE->value,
            ]
        )->plainTextToken;

        $model   = SystemStatistic::factory()->create();
        $payload = [];

        $response = $this->withToken($tokenString)
            ->deleteJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(403)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_METHOD_NOT_ALLOWED->value,
                ],
            ]
        );
    }
}
