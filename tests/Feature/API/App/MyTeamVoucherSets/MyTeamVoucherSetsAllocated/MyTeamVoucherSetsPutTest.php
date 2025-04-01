<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\App\MyTeamVoucherSets\MyTeamVoucherSetsAllocated;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\VoucherSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamVoucherSetsPutTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team-voucher-sets-allocated';

    #[Test]
    public function authentication_required(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $model = VoucherSet::factory()->create();

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function standard_user_without_permission_cannot_access()
    {
        $this->user = $this->createUserWithTeam();

        $model = VoucherSet::factory()->create([
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_cannot_update()
    {
        $this->user = $this->createUserWithTeam();

        $model = VoucherSet::factory()->create([
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_UPDATE->value,
        ]);

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
