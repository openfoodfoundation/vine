<?php

namespace Tests\Feature\API\App\MyProfile;

use App\Enums\PersonalAccessTokenAbility;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyProfilePostTest extends BaseAPITestCase
{
    protected string $endPoint = '/my-profile';

    #[Test]
    public function authentication_required(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function standard_user_without_permission_cannot_access()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_cannot_post()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_PROFILE_CREATE->value,
        ]);

        $response = $this->postJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(403);
    }
}
