<?php

namespace Tests\Feature\API\Admin\AdminUserPersonalAccessTokens;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminTokensPutTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/user-personal-access-tokens';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $randomId = $this->faker->randomDigitNotNull();

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $randomId);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotUpdateAToken()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $randomId = $this->faker->randomDigitNotNull();

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $randomId);

        $response->assertStatus(403);
    }
}
