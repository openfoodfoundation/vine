<?php

namespace Tests\Feature\API\App\VoucherRedemption;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherRedemptionGetTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/voucher-redemptions';

    /**
     * @return void
     */
    #[Test]
    public function itFailsToGetIndividualItemEveryTime()
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

        $voucher = Voucher::factory()
            ->create(
                [
                    'voucher_value_original'  => 1000,
                    'voucher_value_remaining' => 1000,
                    'voucher_set_id'          => $voucherSet->id,
                ]
            );

        $voucherRedemption = VoucherRedemption::factory()
            ->create(
                [
                    'voucher_id'          => $voucher->id,
                    'voucher_set_id'      => $voucherSet->id,
                    'redeemed_by_user_id' => $this->user->id,
                ]
            );

        $response = $this->getJson($this->apiRoot . $this->endPoint . '/' . $voucherRedemption->id);
        $response->assertStatus(401);
    }

    /**
     * @return void
     */
    #[Test]
    public function itFailsToGetAllItemsEveryTime()
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

        $voucher = Voucher::factory()
            ->create(
                [
                    'voucher_value_original'  => 1000,
                    'voucher_value_remaining' => 1000,
                    'voucher_set_id'          => $voucherSet->id,
                ]
            );

        $voucherRedemption = VoucherRedemption::factory(10)
            ->create(
                [
                    'voucher_id'          => $voucher->id,
                    'voucher_set_id'      => $voucherSet->id,
                    'redeemed_by_user_id' => $this->user->id,
                    'redeemed_amount'     => 1
                ]
            );

        $response = $this->getJson($this->apiRoot . $this->endPoint);
        $response->assertStatus(401);
    }
}
