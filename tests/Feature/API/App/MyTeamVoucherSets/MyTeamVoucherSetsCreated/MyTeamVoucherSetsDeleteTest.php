<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Feature\API\App\MyTeamVoucherSets\MyTeamVoucherSetsCreated;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\VoucherSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamVoucherSetsDeleteTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team-voucher-sets-created';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $model = VoucherSet::factory()->create();

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotAccess()
    {
        $this->user = $this->createUserWithTeam();

        $model = VoucherSet::factory()->create();

        Sanctum::actingAs($this->user);

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCannotDelete()
    {
        $this->user = $this->createUserWithTeam();

        $model = VoucherSet::factory()->create();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_DELETE->value,
        ]);

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
