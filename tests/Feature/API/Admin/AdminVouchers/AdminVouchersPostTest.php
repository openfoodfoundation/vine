<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\Admin\AdminVouchers;

use App\Models\Team;
use App\Models\Voucher;
use App\Models\VoucherSet;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVouchersPostTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/vouchers';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createUser();

        $response = $this->post($this->apiRoot . $this->endpoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function onlyAdminCanAccess()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->post($this->apiRoot . $this->endpoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function adminCanNotSaveData()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $team = Team::factory()
            ->create();

        $voucherSet = Voucher::factory()
            ->create([
                'created_by_team_id' => $team->id,
            ]);

        $payload = [
            'voucher_set_id'          => $voucherSet->id,
            'team_id'                 => $team->id,
            'voucher_value_original'  => fake()->randomDigitNotNull,
            'voucher_value_remaining' => fake()->randomDigitNotNull,
            'last_redemption_at'      => now(),
        ];

        $response = $this->post($this->apiRoot . $this->endpoint, $payload);
        $response->assertStatus(403);
    }
}
