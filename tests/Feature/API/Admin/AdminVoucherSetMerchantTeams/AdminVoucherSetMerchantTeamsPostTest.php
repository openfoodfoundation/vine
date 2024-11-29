<?php

namespace Tests\Feature\API\Admin\AdminVoucherSetMerchantTeams;

use App\Enums\ApiResponse;
use App\Models\Team;
use App\Models\TeamMerchantTeam;
use App\Models\TeamUser;
use App\Models\User;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherSetMerchantTeamsPostTest extends BaseAPITestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public string $endpoint = '/admin/voucher-set-merchant-teams';

    #[Test]
    public function onlyAdminCanAccess(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user);

        $payload = [];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function itCanStoreAnItem()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $user                     = User::factory()->create();
        $serviceTeam              = Team::factory()->create();
        $voucherSet               = VoucherSet::factory()->create(
            [
                'allocated_to_service_team_id' => $serviceTeam->id,
            ]
        );
        $team                     = Team::factory()->create();
        $associationToServiceTeam = TeamMerchantTeam::factory()->create(
            [
                'team_id'          => $serviceTeam->id,
                'merchant_team_id' => $team->id,
            ]
        );

        $payload = [
            'voucher_set_id'   => $voucherSet->id,
            'merchant_team_id' => $team->id,
        ];

        $response    = $this->postJson($this->apiRoot . $this->endpoint, $payload);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);
        $this->assertEquals($payload['voucher_set_id'], $responseObj->data->voucher_set_id);
        $this->assertEquals($payload['merchant_team_id'], $responseObj->data->merchant_team_id);
    }

    #[Test]
    public function itRequiresTheMerchantTeamToBeAMerchantOfTheVOucherSetServiceTeam()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $user        = User::factory()->create();
        $serviceTeam = Team::factory()->create();
        $voucherSet  = VoucherSet::factory()->create(
            [
                'allocated_to_service_team_id' => $serviceTeam->id,
            ]
        );
        $team        = Team::factory()->create();


        $payload = [
            'voucher_set_id'   => $voucherSet->id,
            'merchant_team_id' => $team->id,
        ];

        $response    = $this->postJson($this->apiRoot . $this->endpoint, $payload);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(400);

        $this->assertEquals($responseObj->meta->message, ApiResponse::RESPONSE_INVALID_MERCHANT_TEAM_FOR_SERVICE_TEAM->value);
    }


    #[Test]
    public function itCreatesRequests()
    {
        Notification::fake();
        VoucherSetMerchantTeamApprovalRequest::where('id', '>=', 1)->delete();

        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $user                     = User::factory()->create();
        $serviceTeam              = Team::factory()->create();
        $voucherSet               = VoucherSet::factory()->create(
            [
                'allocated_to_service_team_id' => $serviceTeam->id,
            ]
        );
        $team                     = Team::factory()->create();
        $associationToServiceTeam = TeamMerchantTeam::factory()->create(
            [
                'team_id'          => $serviceTeam->id,
                'merchant_team_id' => $team->id,
            ]
        );
        $userTeam                 = TeamUser::factory()->create(
            [
                'user_id' => $user->id,
                'team_id' => $team->id,
            ]
        );

        $payload = [
            'voucher_set_id'   => $voucherSet->id,
            'merchant_team_id' => $team->id,
        ];

        $response    = $this->postJson($this->apiRoot . $this->endpoint, $payload);
        $responseObj = json_decode($response->getContent());

        $response->assertStatus(200);

        $model = VoucherSetMerchantTeamApprovalRequest::first();

        $this->assertEquals($model->merchant_team_id, $team->id);
        $this->assertEquals($model->merchant_user_id, $user->id);
        $this->assertEquals($model->voucher_set_id, $voucherSet->id);
    }
}
