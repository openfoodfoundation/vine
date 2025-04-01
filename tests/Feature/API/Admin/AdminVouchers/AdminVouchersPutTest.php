<?php

namespace Tests\Feature\API\Admin\AdminVouchers;

use App\Models\Voucher;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVouchersPutTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/vouchers';

    #[Test]
    public function it_fails_if_not_authenticated()
    {
        $this->user = $this->createUserWithTeam();

        $voucher = Voucher::factory()
            ->create();

        $response = $this->put($this->apiRoot . $this->endpoint . '/' . $voucher->id);
        $response->assertStatus(302);
    }

    #[Test]
    public function only_admin_can_access()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucher = Voucher::factory()
            ->create();

        $response = $this->put($this->apiRoot . $this->endpoint . '/' . $voucher->id);
        $response->assertStatus(302);
    }

    #[Test]
    public function admin_can_not_update_data()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucher = Voucher::factory()
            ->create();

        $payload = [
            'voucher_set_id'          => $voucher->voucher_set_id,
            'team_id'                 => $voucher->created_by_team_id,
            'voucher_value_original'  => $voucher->voucher_value_original,
            'voucher_value_remaining' => fake()->randomDigitNotNull,
            'last_redemption_at'      => now(),
        ];

        $response = $this->put($this->apiRoot . $this->endpoint . '/' . $voucher->id, $payload);
        $response->assertStatus(403);
    }
}
