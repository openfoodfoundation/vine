<?php

namespace Tests\Feature\API\Admin\AdminUserPersonalAccessTokens;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTokensGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/user-personal-access-tokens';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanNotGetAllTokens()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(403);
    }

    #[Test]
    public function itCanNotGetASingleToken()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $randomId = $this->faker->randomDigitNotNull();

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/' . $randomId);

        $response->assertStatus(403);
    }
}
