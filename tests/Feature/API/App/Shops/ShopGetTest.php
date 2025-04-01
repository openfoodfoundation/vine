<?php

namespace Tests\Feature\API\App\Shops;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class ShopGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/shops';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotAccess()
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
    public function itCanNotGetAllResources()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SHOPS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(403);

    }

    #[Test]
    public function itCanNotGetASingleResource()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SHOPS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(403);
    }
}
