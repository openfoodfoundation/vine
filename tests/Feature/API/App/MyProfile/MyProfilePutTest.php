<?php

namespace Tests\Feature\API\App\MyProfile;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\Team;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyProfilePutTest extends BaseAPITestCase
{
    protected string $endPoint = '/my-profile';

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
            PersonalAccessTokenAbility::MY_PROFILE_UPDATE->value,
        ]);

        $payload = [
            'password' => 'Fcotuser!80',
        ];

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $this->user->id, $payload);
        $response->assertStatus(200);

    }

    #[Test]
    public function itCanNotUpdateNoLetters()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_PROFILE_UPDATE->value,
        ]);

        $payload = [
            'password' => '!80',
        ];

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $this->user->id, $payload);
        $response->assertStatus(400);

    }

    #[Test]
    public function itCanNotUpdateNoNumbers()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_PROFILE_UPDATE->value,
        ]);

        $payload = [
            'password' => '!abcdefg',
        ];

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $this->user->id, $payload);
        $response->assertStatus(400);

    }

    #[Test]
    public function itCanNotUpdateNoSymbols()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_PROFILE_UPDATE->value,
        ]);

        $payload = [
            'password' => 'abcdefgDD80',
        ];

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $this->user->id, $payload);
        $response->assertStatus(400);

    }

    #[Test]
    public function itCanNotUpdateNoStEnoughCharacters()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_PROFILE_UPDATE->value,
        ]);

        $payload = [
            'password' => 'aD8!',
        ];

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $this->user->id, $payload);
        $response->assertStatus(400);

    }
}
