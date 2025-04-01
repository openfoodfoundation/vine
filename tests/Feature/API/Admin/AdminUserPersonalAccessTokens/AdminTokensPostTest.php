<?php

namespace Tests\Feature\API\Admin\AdminUserPersonalAccessTokens;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTokensPostTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/user-personal-access-tokens';

    #[Test]
    public function only_admin_can_access(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $payload = [];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_store_a_token()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $tokenAbilities = PersonalAccessTokenAbility::cases();
        $rand           = rand(0, (count($tokenAbilities) - 1));

        $user                  = User::factory()->create();
        $tokenAbility          = $tokenAbilities[$rand];
        $tokenAbilitiesArray[] = $tokenAbility->value;

        $payload = [
            'user_id'         => $user->id,
            'name'            => $this->faker->word(),
            'token_abilities' => $tokenAbilitiesArray,
        ];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_can_not_store_a_token_if_token_not_exist()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $user = User::factory()->create();

        $tokenAbilitiesArray[] = $this->faker->word();

        $payload = [
            'user_id'       => $user->id,
            'name'          => $this->faker->word(),
            'token_ability' => $tokenAbilitiesArray,
        ];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(400);
    }
}
