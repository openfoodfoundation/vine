<?php

namespace Tests\Feature\API\Admin\AdminSearch;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminSearchPutTest extends BaseAPITestCase
{
    use WithFaker;

    private string $endPoint = '/admin/search';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createAdminUser();

        $response = $this->put($this->apiRoot . $this->endPoint . '/1', []);
        $response->assertStatus(302);
    }

    #[Test]
    public function itFailsToUpdateEveryTime()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->put($this->apiRoot . $this->endPoint . '/1', []);
        $response->assertStatus(403);
    }
}
