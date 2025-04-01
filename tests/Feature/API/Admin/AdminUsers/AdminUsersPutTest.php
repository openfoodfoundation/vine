<?php

namespace Tests\Feature\API\Admin\AdminUsers;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminUsersPutTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/users';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $model = User::factory()->create();

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanUpdateAUser()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = User::factory()->create([
            'is_admin' => 0,
        ]);

        $payload = [
            'is_admin' => 1,
        ];

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(200);

        $responseObj = json_decode($response->getContent());
        $this->assertEquals(1, $responseObj->data->is_admin);
    }
}
