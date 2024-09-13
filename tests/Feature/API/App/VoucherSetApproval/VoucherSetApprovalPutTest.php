<?php

/** @noinspection PhpUndefinedMethodInspection */

namespace Tests\Feature\API\App\VoucherSetApproval;

use App\Enums\VoucherSetMerchantTeamApprovalRequestStatus;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherSetApprovalPutTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/voucher-set-approval';

    #[Test]
    public function standardUserWithoutPermissionCanAccess()
    {
        $model = VoucherSetMerchantTeamApprovalRequest::factory()->create();

        $payload = [
            'approval_status' => VoucherSetMerchantTeamApprovalRequestStatus::APPROVED->value,
        ];

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $model->id, $payload);
        $response->assertStatus(200);

        $statusAfter = VoucherSetMerchantTeamApprovalRequest::find($model->id);
        $this->assertEquals(VoucherSetMerchantTeamApprovalRequestStatus::APPROVED->value, $statusAfter->approval_status);
    }
}
