<?php

namespace Tests\Feature\API\App\MyProfile;

use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyProfilePostTest extends BaseAPITestCase
{

    protected string $endPoint = '/my-profile';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotAccess()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotPost()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_PROFILE_CREATE->value,
        ]);

        $response = $this->postJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(403);
    }
}
