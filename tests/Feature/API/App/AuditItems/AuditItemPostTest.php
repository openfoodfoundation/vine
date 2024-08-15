<?php

namespace Tests\Feature\API\App\AuditItems;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Models\AuditItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AuditItemPostTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/my-team-audit-items';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $model = AuditItem::factory()->create();

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

        $model = AuditItem::factory()->create([
            'team_id' => $this->user->current_team_id,
        ]);

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
            abilities: [PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_CREATE->value]
        );

        $model = AuditItem::factory()->create([
            'team_id' => $this->user->current_team_id,
        ]);

        $response = $this->postJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(403);
    }
}
