<?php

namespace Tests\Feature\API\Admin\AdminVoucherRedemptions;

use App\Models\VoucherRedemption;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherRedemptionsDeleteTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/voucher-redemptions';

    use WithFaker;

    #[Test]
    public function it_fails_if_not_authenticated()
    {
        $this->user = $this->createUserWithTeam();

        $model = VoucherRedemption::factory()
            ->create();

        $response = $this->delete($this->apiRoot . $this->endpoint . '/' . $model->id);
        $response->assertStatus(302);
    }

    #[Test]
    public function only_admin_can_access()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $model = VoucherRedemption::factory()->create();

        $response = $this->delete($this->apiRoot . $this->endpoint . '/' . $model->id);
        $response->assertStatus(302);
    }

    #[Test]
    public function admin_can_not_delete_data()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = VoucherRedemption::factory()
            ->create();

        $response = $this->delete($this->apiRoot . $this->endpoint . '/' . $model->id);
        $response->assertStatus(403);
    }
}
