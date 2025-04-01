<?php

/** @noinspection PhpUndefinedFieldInspection */

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

class VoucherSetApprovalRequestsGetTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/my-team-vsmtar';

    #[Test]
    public function it_fails_if_not_authenticated()
    {
        Event::fake();

        $this->user = $this->createUser();

        $voucherSet = VoucherSet::factory()->create();

        VoucherSetMerchantTeamApprovalRequest::factory()
            ->create(
                [
                    'voucher_set_id' => $voucherSet->id,
                ]
            );

        $response = $this->getJson($this->apiRoot . $this->endPoint);
        $response->assertStatus(401);
    }

    #[Test]
    public function it_fails_to_get_a_single_item_if_not_authenticated()
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

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(401);
    }

    #[Test]
    public function it_fails_to_get_without_ability()
    {
        Event::fake();

        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->create();

        VoucherSetMerchantTeamApprovalRequest::factory()
            ->create(
                [
                    'voucher_set_id' => $voucherSet->id,
                ]
            );

        $response = $this->getJson($this->apiRoot . $this->endPoint);
        $response->assertStatus(401);
    }

    #[Test]
    public function it_fails_to_get_a_single_item_without_ability()
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

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(401);
    }

    #[Test]
    public function it_can_get_all_items_with_ability()
    {
        Event::fake();

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_READ->value,
        ]);

        $voucherSet = VoucherSet::factory()->create();

        VoucherSetMerchantTeamApprovalRequest::factory()->create([
            'merchant_user_id' => $this->user->id,
            'voucher_set_id'   => $voucherSet->id,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint);
        $response->assertStatus(200);
    }

    #[Test]
    public function it_can_get_a_single_item_with_ability()
    {
        Event::fake();

        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_READ->value,
        ]);

        $voucherSet = VoucherSet::factory()->create();

        $model = VoucherSetMerchantTeamApprovalRequest::factory()->create([
            'merchant_user_id' => $this->user->id,
            'voucher_set_id'   => $voucherSet->id,
        ]);

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(200);

        $responseObj = json_decode($response->getContent());
        $this->assertEquals($responseObj->data->id, $model->id);
    }
}
