<?php

namespace Tests\Feature\API\Admin\AdminVoucherSetMerchantTeams;

use App\Models\Team;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherSetMerchantTeamsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/admin/voucher-set-merchant-teams';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $response = $this->getJson($this->apiRoot . $this->endpoint);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanGetAllItems()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $existing   = VoucherSetMerchantTeam::count();
        $rand       = rand(5, 10);
        $voucherSet = VoucherSet::factory()->create();
        $team       = Team::factory()->create();
        $model      = VoucherSetMerchantTeam::factory($rand)->create(
            [
                'voucher_set_id'   => $voucherSet->id,
                'merchant_team_id' => $team->id,
            ]
        );

        $response    = $this->getJson($this->apiRoot . $this->endpoint);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($existing + $rand, $responseObj->data->total);
    }

    #[Test]
    public function itCanGetASingleItem()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->create();
        $team       = Team::factory()->create();
        $model      = VoucherSetMerchantTeam::factory()->create(
            [
                'voucher_set_id'   => $voucherSet->id,
                'merchant_team_id' => $team->id,
            ]
        );

        $response = $this->getJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(200);
    }
}
