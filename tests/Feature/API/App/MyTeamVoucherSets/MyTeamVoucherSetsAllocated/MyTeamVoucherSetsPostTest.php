<?php

namespace Feature\API\App\MyTeamVoucherSets\MyTeamVoucherSetsAllocated;

use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamVoucherSetsPostTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team-voucher-sets-allocated';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotAccess()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotPost()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_CREATE->value,
        ]);

        $response = $this->postJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(403);
    }
}
