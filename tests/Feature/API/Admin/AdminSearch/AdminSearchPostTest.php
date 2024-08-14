<?php

namespace Tests\Feature\API\Admin\AdminSearch;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminSearchPostTest extends BaseAPITestCase
{
    use WithFaker;

    private string $endPoint = '/admin/search';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createAdminUser();

        $response = $this->post($this->apiRoot . $this->endPoint, []);
        $response->assertStatus(302);
    }

    #[Test]
    public function itFailsToCreateEveryTime()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->post($this->apiRoot . $this->endPoint, []);
        $response->assertStatus(403);
    }
}
