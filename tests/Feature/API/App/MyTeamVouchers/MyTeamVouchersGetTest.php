<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\App\MyTeamVouchers;

use App\Enums\PersonalAccessTokenAbility;
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

    protected string $endPoint = '/my-team-vouchers';

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
            'voucher_set_id'     => $voucherSet->id,
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

        $voucherSet = VoucherSet::factory()->create();

        $rand = rand(5, 20);

        Voucher::factory($rand)->create([
            'voucher_set_id'     => $voucherSet->id,
            'created_by_team_id' => $this->user->current_team_id,
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
    public function itCanGetAllItemsAllocatedToUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $voucherSet = VoucherSet::factory()->create();

        $rand = rand(5, 20);

        Voucher::factory($rand)->create([
            'voucher_set_id'               => $voucherSet->id,
            'allocated_to_service_team_id' => $this->user->current_team_id,
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
    public function itCanGetOnlyItemsCreatedByOrAllocatedToUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $voucherSet = VoucherSet::factory()->create();

        $rand = rand(5, 20);

        Voucher::factory($rand)->create([
            'voucher_set_id'     => $voucherSet->id,
            'created_by_team_id' => $this->user->current_team_id,
        ]);

        $rand2 = rand(5, 20);

        Voucher::factory($rand2)->create([
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
    public function itCanGetASingleItemCreatedByUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $voucherSet = VoucherSet::factory()->create([
            'created_by_team_id' => $this->user->current_team_id,
        ]);

        $model = Voucher::factory()->create([
            'voucher_set_id'     => $voucherSet->id,
            'created_by_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());

        $modelWithShortCode = Voucher::find($model->id);
        $this->assertEquals($modelWithShortCode->voucher_short_code, $responseObj->data->voucher_short_code);
    }

    #[Test]
    public function itCanGetASingleItemAllocatedToUserTeam()
    {
        $this->user = $this->createUserWithTeam();

        $voucherSet = VoucherSet::factory()->create();

        $model = Voucher::factory()->create([
            'voucher_set_id'               => $voucherSet->id,
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::MY_TEAM_VOUCHERS_READ->value,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $model->id);

        $response->assertStatus(200);
        $responseObj        = json_decode($response->getContent());
        $modelWithShortCode = Voucher::find($model->id);
        $this->assertEquals($modelWithShortCode->voucher_short_code, $responseObj->data->voucher_short_code);
    }
}
