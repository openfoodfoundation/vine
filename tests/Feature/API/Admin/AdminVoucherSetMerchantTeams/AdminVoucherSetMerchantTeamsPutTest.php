<?php

namespace Tests\Feature\API\Admin\AdminVoucherSetMerchantTeams;

use App\Models\Team;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherSetMerchantTeamsPutTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/voucher-set-merchant-teams';

    #[Test]
    public function only_admin_can_access(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $voucherSet = VoucherSet::factory()->create();
        $team       = Team::factory()->create();
        $model      = VoucherSetMerchantTeam::factory()->create(
            [
                'voucher_set_id'   => $voucherSet->id,
                'merchant_team_id' => $team->id,
            ]
        );

        $payload = [];

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_not_update_an_item()
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

        $payload = [];

        $response = $this->putJson($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);

        $response->assertStatus(403);
    }
}
