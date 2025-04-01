<?php

namespace Tests\Feature\API\App\MyTeam;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamDeleteTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team';

    #[Test]
    public function authentication_required(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $model = Team::factory()->create();

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function standard_user_without_permission_cannot_access()
    {
        $this->user = $this->createUserWithTeam();

        $model = Team::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_cannot_delete()
    {
        $this->user = $this->createUserWithTeam();

        $model = Team::factory()->create();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_DELETE->value,
        ]);

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
