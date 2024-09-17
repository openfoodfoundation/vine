<?php

/** @noinspection PhpUndefinedFieldInspection */
/** @noinspection SpellCheckingInspection */
/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Feature\API\App\VoucherSetApprovalRequests;

use App\Enums\PersonalAccessTokenAbility;
use App\Enums\VoucherSetMerchantTeamApprovalRequestStatus;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherSetApprovalRequestsPutTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/vsmtar';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        Event::fake();

        $this->user = $this->createUser();

        $voucherSet = VoucherSet::factory()->create();

        $model = VoucherSetMerchantTeamApprovalRequest::factory()
            ->create(
                [
                    'voucher_set_id' => $voucherSet->id,
                ]
            );

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(401);
    }

    #[Test]
    public function itFailsUpdateWithoutAbility()
    {
        Event::fake();

        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->create();

        $model = VoucherSetMerchantTeamApprovalRequest::factory()
            ->create(
                [
                    'voucher_set_id' => $voucherSet->id,
                ]
            );

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(401);
    }

    #[Test]
    public function itCanUpdateWithAbility()
    {
        Event::fake();

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_UPDATE->value,
        ]);

        $voucherSet = VoucherSet::factory()->create();

        $model = VoucherSetMerchantTeamApprovalRequest::factory()->create([
            'merchant_user_id' => $this->user->id,
            'voucher_set_id'   => $voucherSet->id,
        ]);

        $payload = [
            'approval_status' => VoucherSetMerchantTeamApprovalRequestStatus::APPROVED->value,
        ];

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $model->id, $payload);
        $response->assertStatus(200);

        $statusAfter = VoucherSetMerchantTeamApprovalRequest::find($model->id);
        $this->assertEquals(VoucherSetMerchantTeamApprovalRequestStatus::APPROVED->value, $statusAfter->approval_status);
    }
}
