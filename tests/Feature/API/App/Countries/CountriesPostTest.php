<?php

namespace Tests\Feature\API\App\Countries;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Models\AuditItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class CountriesPostTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/countries';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->postJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotCreateWithIncorrectToken()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->postJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );
    }

    #[Test]
    public function itCannotCreateWithCorrectToken()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user,
            abilities: [PersonalAccessTokenAbility::COUNTRIES_CREATE->value]
        );

        $response = $this->postJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(403);
    }
}
