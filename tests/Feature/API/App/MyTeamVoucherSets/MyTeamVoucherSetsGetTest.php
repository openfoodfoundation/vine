<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\App\MyTeamVoucherSets;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\VoucherSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamVoucherSetsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team-voucher-sets';

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
    public function itCanGetAllItemsAllocatedToUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $rand = rand(5, 20);

        VoucherSet::factory($rand)->create([
            'allocated_to_service_team_id' => $this->user->current_team_id,
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
    public function itCanGetOnlyItemsCreatedByOrAllocatedToUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $rand = rand(5, 20);

        VoucherSet::factory($rand)->create([
            'created_by_team_id' => $this->user->current_team_id,
        ]);

        $rand2 = rand(5, 20);

        VoucherSet::factory($rand2)->create([
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
    public function itCanGetASingleItemCreatedByUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $model = VoucherSet::factory()->create([
            'created_by_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertEquals($model->id, $responseObj->data->id);
    }

    #[Test]
    public function itCanGetASingleItemAllocatedToUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $model = VoucherSet::factory()->create([
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHER_SETS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertEquals($model->id, $responseObj->data->id);
    }
}
