<?php

namespace Tests\Feature\API\Admin\AdminSystemStatistics;

use App\Models\SystemStatistic;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminSystemStatisticPutTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/system-statistics';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $model = SystemStatistic::factory()->create();

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotUpdateAResource()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = SystemStatistic::factory()->create();

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
