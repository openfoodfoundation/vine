<?php


namespace Tests\Feature\API\App\MyProfile;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\Team;

use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyProfileGetTest extends BaseAPITestCase
{

    protected string $endPoint = '/my-profile';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotAccess()
    {
        $this->user = $this->createUserWithTeam();

        Team::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanGetData()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_PROFILE_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertEquals($responseObj->data->id, $this->user->id);
        $this->assertEquals($responseObj->data->current_team_id, $this->user->current_team_id);
        $this->assertEquals($responseObj->data->name, $this->user->name);
    }

    #[Test]
    public function itCanGetDataFromIdSuffix()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_PROFILE_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/1234');

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertEquals($responseObj->data->id, $this->user->id);
        $this->assertEquals($responseObj->data->current_team_id, $this->user->current_team_id);
        $this->assertEquals($responseObj->data->name, $this->user->name);
    }


}
