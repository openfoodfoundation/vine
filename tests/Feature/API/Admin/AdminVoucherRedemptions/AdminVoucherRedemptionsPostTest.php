<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\Admin\AdminVoucherRedemptions;

use App\Models\Voucher;
use App\Models\VoucherSet;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherRedemptionsPostTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/voucher-redemptions';

    #[Test]
    public function it_fails_if_not_authenticated()
    {
        $this->user = $this->createUser();

        $response = $this->post($this->apiRoot . $this->endpoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function only_admin_can_access()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->post($this->apiRoot . $this->endpoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function admin_can_not_save_data()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->create();

        $voucher = Voucher::factory()->create([
            'voucher_set_id' => $voucherSet->id,
        ]);

        $payload = [
            'voucher_id'      => $voucherSet->id,
            'redeemed_amount' => $voucher->voucher_value_remaining,
        ];

        $response = $this->post($this->apiRoot . $this->endpoint, $payload);
        $response->assertStatus(403);
    }
}
