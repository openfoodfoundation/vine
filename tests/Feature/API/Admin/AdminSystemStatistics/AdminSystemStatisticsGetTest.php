<?php

namespace Tests\Feature\API\Admin\AdminSystemStatistics;

use App\Models\SystemStatistic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminSystemStatisticsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/system-statistics';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanGetAllResources()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $rand = rand(5, 10);

        SystemStatistic::factory($rand)->create();

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(200);

    }

    #[Test]
    public function itCanGetASingleResource()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = SystemStatistic::factory()->create();

        $response    = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($model->num_users, $responseObj->data->num_users);
    }
}
