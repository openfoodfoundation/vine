<?php

namespace Tests\Feature\API\Admin\AdminUserPersonalAccessTokens;

use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTokensGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/user-personal-access-tokens';

    #[Test]
    public function only_admin_can_access(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_get_all_tokens()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $tokenName = $this->faker->name();

        $tokenAbilities        = PersonalAccessTokenAbility::cases();
        $rand                  = rand(0, (count($tokenAbilities) - 1));
        $tokenAbility          = $tokenAbilities[$rand];
        $tokenAbilitiesArray[] = $tokenAbility->value;

        $this->user->createToken($tokenName, $tokenAbilitiesArray);

        $response    = $this->getJson($this->apiRoot . $this->endpoint);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($tokenName, $responseObj->data->data[0]->name);
    }

    #[Test]
    public function it_can_get_a_single_token()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $tokenName = $this->faker->name();

        $tokenAbilities        = PersonalAccessTokenAbility::cases();
        $rand                  = rand(0, (count($tokenAbilities) - 1));
        $tokenAbility          = $tokenAbilities[$rand];
        $tokenAbilitiesArray[] = $tokenAbility->value;

        $this->user->createToken($tokenName, $tokenAbilitiesArray);

        $tokens  = $this->user->tokens;
        $tokenId = json_decode($tokens[0]->id);

        $response    = $this->getJson($this->apiRoot . $this->endpoint . '/' . $tokenId);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($tokenName, $responseObj->data->name);
    }
}
