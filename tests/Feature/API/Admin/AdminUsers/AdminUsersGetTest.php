<?php

namespace Tests\Feature\API\Admin\AdminUsers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminUsersGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/users';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanGetAllUsers()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $rand = rand(5, 10);

        User::factory($rand)->create();

        $response    = $this->getJson($this->apiRoot . $this->endpoint);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $numTotalUser = $rand + 1; //adding admin user to created users
        $this->assertEquals($numTotalUser, $responseObj->data->total);
    }

    #[Test]
    public function itCanGetASingleUser()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = User::factory()->create();

        $response    = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($model->name, $responseObj->data->name);
    }
}
