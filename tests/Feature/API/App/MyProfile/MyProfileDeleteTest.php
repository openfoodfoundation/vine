<?php

namespace Tests\Feature\API\App\MyProfile;

use App\Enums\PersonalAccessTokenAbility;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyProfileDeleteTest extends BaseAPITestCase
{
    protected string $endPoint = '/my-profile';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $this->user->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotAccess()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $this->user->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotDelete()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_PROFILE_DELETE->value,
        ]);

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $this->user->id);

        $response->assertStatus(403);
    }
}
