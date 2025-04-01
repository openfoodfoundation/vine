<?php

namespace Tests\Feature\API\App\Countries;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class CountriesDeleteTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/countries';

    #[Test]
    public function authentication_required(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(401);
    }

    #[Test]
    public function it_cannot_delete_with_incorrect_token()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

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
    public function it_cannot_delete_with_correct_token()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user,
            abilities: [PersonalAccessTokenAbility::COUNTRIES_DELETE->value]
        );

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/1');

        $response->assertStatus(403);
    }
}
