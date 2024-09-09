<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\Admin\AdminVoucherSets;

use App\Models\Team;
use App\Models\VoucherSet;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherSetsPostTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/voucher-sets';

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

        $voucherSet = VoucherSet::factory()
            ->create([
                'created_by_team_id' => $team->id,
                'created_by_user_id' => $this->user->id,
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
