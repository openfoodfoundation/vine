<?php

namespace Tests\Feature\API\App\Countries;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class CountriesPutTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/countries';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotUpdateWithIncorrectToken()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );
    }

    #[Test]
    public function itCannotUpdateWithCorrectToken()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user,
            abilities: [PersonalAccessTokenAbility::COUNTRIES_UPDATE->value]
        );

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(403);
    }
}
