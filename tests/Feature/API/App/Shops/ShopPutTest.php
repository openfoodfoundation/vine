<?php

namespace Tests\Feature\API\App\Shops;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class ShopPutTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/shops';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/1', []);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotUpdate()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/1', []);

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );

    }

    #[Test]
    public function itCanNotUpdateASingleResource()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SHOPS_UPDATE->value,
        ]);

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/1', []);

        $response->assertStatus(403);
    }
}
