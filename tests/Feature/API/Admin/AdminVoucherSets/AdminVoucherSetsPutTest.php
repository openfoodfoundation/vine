<?php

namespace Tests\Feature\API\Admin\AdminVoucherSets;

use App\Models\Voucher;
use App\Models\VoucherSet;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherSetsPutTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/voucher-sets';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createUserWithTeam();

        $voucher = VoucherSet::factory()
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

        $voucher = VoucherSet::factory()
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

        $voucher = VoucherSet::factory()
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
