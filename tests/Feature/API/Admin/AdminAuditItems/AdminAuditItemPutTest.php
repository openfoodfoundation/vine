<?php

namespace Tests\Feature\API\Admin\AdminAuditItems;

use App\Models\AuditItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminAuditItemPutTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/audit-items';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $model = AuditItem::factory()->create();

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

        $model = AuditItem::factory()->create();

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
