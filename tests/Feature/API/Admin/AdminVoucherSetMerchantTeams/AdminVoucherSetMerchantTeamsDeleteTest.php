<?php

namespace Tests\Feature\API\Admin\AdminVoucherSetMerchantTeams;

use App\Enums\VoucherSetMerchantTeamApprovalRequestStatus;
use App\Models\Team;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeam;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherSetMerchantTeamsDeleteTest extends BaseAPITestCase
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

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_delete_a_voucher_set_merchant_team()
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

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_deletes_approval_requests_also()
    {
        Notification::fake();
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

        $requests = VoucherSetMerchantTeamApprovalRequest::factory()->create(
            [
                'voucher_set_id'   => $voucherSet->id,
                'merchant_team_id' => $team->id,
                'merchant_user_id' => $this->user->id,
                'approval_status'  => VoucherSetMerchantTeamApprovalRequestStatus::READY->value,
            ]
        );

        $numRequests = VoucherSetMerchantTeamApprovalRequest::count();

        $this->assertEquals(1, $numRequests);

        $response = $this->deleteJson($this->apiRoot . $this->endpoint . '/' . $model->id);

        $response->assertStatus(200);

        $numRequests1 = VoucherSetMerchantTeamApprovalRequest::count();
        $this->assertEquals(0, $numRequests1);
    }
}
