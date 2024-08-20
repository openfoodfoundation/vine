<?php

namespace Tests\Feature\API\Admin\AdminTeamUsers;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTeamUsersPutTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/team-users';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $user  = User::factory()->create();
        $team  = Team::factory()->create();
        $model = TeamUser::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $payload = [];

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanNotUpdateATeamUser()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $user  = User::factory()->create();
        $team  = Team::factory()->create();
        $model = TeamUser::factory()->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]);

        $payload = [];

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(200);
    }
}
