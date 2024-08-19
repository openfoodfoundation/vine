<?php
/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\Admin\AdminUserPersonalAccessTokens;

use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTokensDeleteTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/user-personal-access-tokens';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $randomId = $this->faker->randomDigitNotNull();

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/' . $randomId);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanDeleteAToken()
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

        $this->user->createToken($tokenName, $tokenAbilitiesArray);

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/' . $tokenId);

        $response->assertStatus(200);
    }
}
