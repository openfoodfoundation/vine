<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Feature\API\App\MyTeamVoucherSets\MyTeamVoucherSetsCreated;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\Team;
use App\Models\VoucherSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamVoucherSetsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team-voucher-sets-created';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotAccess()
    {
        $this->user = $this->createUserWithTeam();

        VoucherSet::factory()->create([
            'created_by_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanGetAllItemsCreatedByUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $rand = rand(5, 20);

        VoucherSet::factory($rand)->create([
            'created_by_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertEquals($rand, $responseObj->data->total);
    }

    #[Test]
    public function itCanNotGetAllItemsAllocatedToUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $rand = rand(5, 20);

        $nonUserTeam = Team::factory()->create();

        VoucherSet::factory($rand)->create([
            'created_by_team_id'           => $nonUserTeam->id,
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertCount(0, $responseObj->data->data);
    }

    #[Test]
    public function itCanGetOnlyItemsCreatedByUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $rand = rand(5, 20);

        VoucherSet::factory($rand)->create([
            'created_by_team_id' => $this->user->current_team_id,
        ]);

        $rand2 = rand(5, 20);

        VoucherSet::factory($rand2)->create([
            'created_by_team_id'           => $this->user->current_team_id + 1,
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        $rand3 = rand(5, 20);

        VoucherSet::factory($rand3)->create([
            'created_by_team_id'           => $this->user->current_team_id + 1,
            'allocated_to_service_team_id' => $this->user->current_team_id + 2,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertEquals($rand, $responseObj->data->total);
    }

    #[Test]
    public function itCanNotGetASingleItem()
    {
        $this->user = $this->createUserWithTeam();

        $nonUserTeam = Team::factory()->create();

        $model = VoucherSet::factory()->create([
            'created_by_team_id'           => $nonUserTeam->id,
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
