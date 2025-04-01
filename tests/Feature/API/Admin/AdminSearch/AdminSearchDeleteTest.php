<?php

namespace Tests\Feature\API\Admin\AdminSearch;

use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminSearchDeleteTest extends BaseAPITestCase
{
    use WithFaker;

    private string $endPoint = '/admin/search';

    #[Test]
    public function it_fails_if_not_authenticated()
    {
        $this->user = $this->createAdminUser();

        $response = $this->get($this->apiRoot . $this->endPoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function it_fails_to_delete_every_time()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->delete($this->apiRoot . $this->endPoint . '/1');
        $response->assertStatus(403);
    }
}
