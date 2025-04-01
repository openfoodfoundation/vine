<?php

namespace Tests\Feature\API\Admin\AdminVoucherRedemptions;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherRedemptionsPutTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/voucher-redemptions';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createUserWithTeam();

        $voucher = VoucherRedemption::factory()
            ->create();

        $response = $this->put($this->apiRoot . $this->endpoint . '/' . $voucher->id);
        $response->assertStatus(302);
    }

    #[Test]
    public function onlyAdminCanAccess()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucher = VoucherRedemption::factory()
            ->create();

        $response = $this->put($this->apiRoot . $this->endpoint . '/' . $voucher->id);
        $response->assertStatus(302);
    }

    #[Test]
    public function adminCanNotUpdateData()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->create();

        $voucher = Voucher::factory()->create([
            'voucher_set_id' => $voucherSet->id,
        ]);

        $model = VoucherRedemption::factory()->create();

        $payload = [
            'voucher_id' => $voucher->id,
            'amount'     => $voucher->voucher_value_remaining,
        ];

        $response = $this->put($this->apiRoot . $this->endpoint . '/' . $model->id, $payload);
        $response->assertStatus(403);
    }
}
