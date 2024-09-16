<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Feature\API\App\VoucherSetApproval;

use App\Enums\VoucherSetMerchantTeamApprovalRequestStatus;
use App\Models\User;
use App\Models\VoucherSet;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherSetApprovalPutTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/voucher-set-approval';

    #[Test]
    public function standardUserWithoutPermissionCanAccess()
    {
        Event::fake();

        $voucherSet   = VoucherSet::factory()->create();
        $merchantUser = User::factory()->create();

        $model = VoucherSetMerchantTeamApprovalRequest::factory()->create([
            'merchant_user_id' => $merchantUser->id,
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
