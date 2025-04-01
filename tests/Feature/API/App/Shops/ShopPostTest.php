<?php

namespace Tests\Feature\API\App\Shops;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class ShopPostTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/shops';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->postJson($this->apiRoot . $this->endpoint, []);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotCreate()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->postJson($this->apiRoot . $this->endpoint, []);

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );

    }

    #[Test]
    public function userWithPermissionCanCreate()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::SHOPS_CREATE->value,
        ]);

        $shopName  = fake()->name();
        $userName  = fake()->name();
        $userEmail = fake()->email();

        $user = User::whereEmail($userEmail)->first();
        self::assertNull($user);

        $payload = [
            'shop_name'  => $shopName,
            'user_name'  => $userName,
            'user_email' => $userEmail,
        ];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(200);

        $responseObj = json_decode($response->getContent(), true);

        self::assertSame(
            'Saved. Here is the API Token for the user linked to this new team. It will only be displayed ONCE, so please store it in a secure manner.',
            $responseObj['meta']['message']
        );

        $user = User::whereEmail($userEmail)->first();
        self::assertNotNull($user);
    }
}
