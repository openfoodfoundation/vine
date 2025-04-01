<?php

namespace Tests\Feature\API\App\AuditItems;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Models\AuditItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AuditItemsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/my-team-audit-items';

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
        $this->user = $this->createUserWithTeam();

        $model = AuditItem::factory()->create([
            'auditable_team_id' => $this->team->id,
        ]);

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
    public function itCanGetAllResources()
    {
        $this->user = $this->createUserWithTeam();

        $num = rand(1, 40);

        AuditItem::factory($num)->create([
            'auditable_team_id' => $this->team->id,
        ]);

        // Different team, should be inaccessible
        AuditItem::factory($num)->create([
            'auditable_team_id' => $this->team->id + 1,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(200);

        $responseObject = json_decode($response->getContent(), false);

        foreach ($responseObject->data->data as $auditItem) {
            self::assertSame($this->user->current_team_id, $auditItem->auditable_team_id);
        }
    }

    #[Test]
    public function itCanNotGetAllResourcesIncorrectAbility()
    {
        $this->user = $this->createUserWithTeam();

        $model = AuditItem::factory()->create([
            'auditable_team_id' => $this->team->id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_UPDATE->value,
        ]);

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
    public function itCanGetASingleResource()
    {
        $this->user = $this->createUserWithTeam();

        $model = AuditItem::factory()->create([
            'auditable_team_id' => $this->team->id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(200);
    }

    #[Test]
    public function itCanNotGetASingleResourceFromAnotherTeam()
    {
        $this->user = $this->createUserWithTeam();

        $model = AuditItem::factory()->create([
            'auditable_team_id' => $this->team->id + 1,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_AUDIT_ITEMS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(404);
    }
}
