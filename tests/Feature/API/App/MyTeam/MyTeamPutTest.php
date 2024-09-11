<?php

namespace Tests\Feature\API\App\MyTeam;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamPutTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $model = Team::factory()->create();

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotAccess()
    {
        $this->user = $this->createUserWithTeam();

        $model = Team::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanUpdate()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_UPDATE->value,
        ]);

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $this->user->current_team_id);
        $response->assertStatus(200);
    }
}
