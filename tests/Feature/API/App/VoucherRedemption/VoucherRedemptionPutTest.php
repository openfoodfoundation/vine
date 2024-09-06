<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\App\VoucherRedemption;

use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherRedemptionPutTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/voucher-redemptions';

    /**
     * @return void
     */
    #[Test]
    public function itFailsToUpdateEveryTime()
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

        $response = $this->putJson($this->apiRoot . $this->endPoint . '/' . $voucherRedemption->id, []);
        $response->assertStatus(401);
    }
}
