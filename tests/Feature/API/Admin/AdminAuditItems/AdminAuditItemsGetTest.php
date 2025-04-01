<?php

namespace Tests\Feature\API\Admin\AdminAuditItems;

use App\Models\AuditItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminAuditItemsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/audit-items';

    #[Test]
    public function only_admin_can_access(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_get_all_resources()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $rand = rand(5, 10);

        AuditItem::factory($rand)->create();

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(200);

    }

    #[Test]
    public function it_can_get_a_single_resource()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = AuditItem::factory()->create();

        $response    = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($model->id, $responseObj->data->id);
    }
}
