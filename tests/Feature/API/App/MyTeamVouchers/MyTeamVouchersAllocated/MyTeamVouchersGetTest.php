<?php

/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\App\MyTeamVouchers\MyTeamVouchersAllocated;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\Team;
use App\Models\Voucher;
use App\Models\VoucherSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamVouchersGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected string $endPoint = '/my-team-vouchers-allocated';

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

        $voucherSet = VoucherSet::factory()->create();

        Voucher::factory()->create([
            'voucher_set_id'               => $voucherSet->id,
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanNotGetAllItemsCreatedByUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $voucherSet = VoucherSet::factory()->create();

        $nonUserTeam = Team::factory()->create();

        $rand = rand(5, 20);

        Voucher::factory($rand)->create([
            'voucher_set_id'               => $voucherSet->id,
            'created_by_team_id'           => $this->user->current_team_id,
            'allocated_to_service_team_id' => $nonUserTeam->id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertCount(0, $responseObj->data->data);
    }

    #[Test]
    public function itCanGetAllItemsAllocatedToUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $voucherSet = VoucherSet::factory()->create();

        $nonUserTeam = Team::factory()->create();

        $rand = rand(5, 20);

        Voucher::factory($rand)->create([
            'voucher_set_id'               => $voucherSet->id,
            'created_by_team_id'           => $nonUserTeam->id,
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertCount($rand, $responseObj->data->data);
    }

    #[Test]
    public function itCanGetOnlyItemsAllocatedToUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $voucherSet = VoucherSet::factory()->create();

        $rand = rand(5, 20);

        Voucher::factory($rand)->create([
            'voucher_set_id'               => $voucherSet->id,
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        $rand2 = rand(5, 20);

        Voucher::factory($rand2)->create([
            'created_by_team_id'           => $this->user->current_team_id,
            'allocated_to_service_team_id' => $this->user->current_team_id + 1,
        ]);

        $rand3 = rand(5, 20);

        Voucher::factory($rand3)->create([
            'created_by_team_id'           => $this->user->current_team_id + 1,
            'allocated_to_service_team_id' => $this->user->current_team_id + 2,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
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

        $voucherSet = VoucherSet::factory()->create();

        $nonUserTeam = Team::factory()->create();

        $model = Voucher::factory()->create([
            'voucher_set_id'               => $voucherSet->id,
            'created_by_team_id'           => $this->user->current_team_id,
            'allocated_to_service_team_id' => $nonUserTeam->id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(403);
    }
}
