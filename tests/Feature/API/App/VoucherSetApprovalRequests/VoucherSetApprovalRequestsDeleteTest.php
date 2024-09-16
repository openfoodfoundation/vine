<?php

namespace Tests\Feature\API\App\VoucherSetApprovalRequests;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherSetApprovalRequestsDeleteTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/vsmtar';

    /**
     * @return void
     */
    #[Test]
    public function itFailsToDeleteEveryTime()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()
            ->create(
                [
                    'created_by_user_id' => $this->user->id,
                ]
            );

        $model = VoucherSetMerchantTeamApprovalRequest::factory()
            ->create(
                [
                    'voucher_set_id'      => $voucherSet->id,
                ]
            );

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/' . $model->id);
        $response->assertStatus(401);
    }
}
