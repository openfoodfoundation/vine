<?php

namespace Tests\Feature\API\Admin\AdminSystemStatistics;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminSystemStatisticPostTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/system-statistics';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $payload = [];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotStoreAResource()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $payload = [];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(403);
    }
}
