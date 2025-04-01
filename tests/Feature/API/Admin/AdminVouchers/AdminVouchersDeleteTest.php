<?php

namespace Tests\Feature\API\Admin\AdminVouchers;

use App\Models\Voucher;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVouchersDeleteTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/vouchers';

    use WithFaker;

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createUserWithTeam();

        $voucher = Voucher::factory()
            ->create();

        $response = $this->delete($this->apiRoot . $this->endpoint . '/' . $voucher->id);
        $response->assertStatus(302);
    }

    #[Test]
    public function onlyAdminCanAccess()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $voucher = Voucher::factory()
            ->create(
                [
                    'created_by_team_id' => $this->user->current_team_id,
                ]
            );

        $response = $this->delete($this->apiRoot . $this->endpoint . '/' . $voucher->id);
        $response->assertStatus(302);
    }

    #[Test]
    public function adminCanNotDeleteData()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucher = Voucher::factory()
            ->create();

        $response = $this->delete($this->apiRoot . $this->endpoint . '/' . $voucher->id);
        $response->assertStatus(403);
    }
}
