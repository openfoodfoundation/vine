<?php

/** @noinspection SpellCheckingInspection */

namespace Tests\Feature\API\App\VoucherSetApprovalRequests;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherSetApprovalRequestsDeleteTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team-vsmtar';

    #[Test]
    public function it_fails_if_not_authenticated()
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

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(401);
    }

    #[Test]
    public function it_fails_to_delete_every_time()
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

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_not_delete()
    {
        Event::fake();

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_DELETE->value,
        ]);

        $voucherSet = VoucherSet::factory()->create();

        $model = VoucherSetMerchantTeamApprovalRequest::factory()
            ->create(
                [
                    'voucher_set_id' => $voucherSet->id,
                ]
            );

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(403);
    }
}
