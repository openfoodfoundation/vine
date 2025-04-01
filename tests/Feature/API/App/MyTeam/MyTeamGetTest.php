<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\App\MyTeam;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamGetTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team';

    #[Test]
    public function authentication_required(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function standard_user_without_permission_cannot_access()
    {
        $this->user = $this->createUserWithTeam();

        Team::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_get_data()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertEquals($responseObj->data->id, $this->user->current_team_id);
    }

    #[Test]
    public function it_can_not_get_a_team()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $this->user->current_team_id);

        $response->assertStatus(403);
    }
}
