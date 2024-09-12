<?php
/** @noinspection PhpUndefinedMethodInspection */

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\Admin\AdminVoucherRedemptions;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherRedemptionsGetTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/voucher-redemptions';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createUserWithTeam();

        $response = $this->get($this->apiRoot . $this->endpoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function onlyAdminCanAccess()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->get($this->apiRoot . $this->endpoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function itReturnsData()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->create();
        $voucher    = Voucher::factory()->create([
            'voucher_set_id' => $voucherSet->id,
        ]);

        $model = VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => $voucher->voucher_value_remaining,
        ]);

        $response = $this->get($this->apiRoot . $this->endpoint);
        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertCount(1, $responseObj->data->data);
        $this->assertEquals($responseObj->data->data[0]->id, $model->id);

        $voucherValueAfterRedeem = Voucher::find($voucher->id);

        $this->assertEquals(0, $voucherValueAfterRedeem->voucher_value_remaining);
    }

    #[Test]
    public function itReturnsSingleData()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->create();
        $voucher    = Voucher::factory()->create([
            'voucher_set_id' => $voucherSet->id,
        ]);

        $model = VoucherRedemption::factory()->create([
            'voucher_id'      => $voucher->id,
            'redeemed_amount' => $voucher->voucher_value_remaining,
        ]);

        $response = $this->get($this->apiRoot . $this->endpoint . '/' . $model->id);
        $response->assertStatus(200);
    }
}
