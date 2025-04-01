<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\Admin\AdminVouchers;

use App\Models\Voucher;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVouchersGetTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/vouchers';

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

        $voucher = Voucher::factory()
            ->create();

        $response = $this->get($this->apiRoot . $this->endpoint);
        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertCount(1, $responseObj->data->data);
        $this->assertEquals($responseObj->data->data[0]->id, $voucher->id);
    }

    #[Test]
    public function itReturnsSingleData()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $voucher = Voucher::factory()
            ->create();

        $response = $this->get($this->apiRoot . $this->endpoint . '/' . $voucher->id);
        $response->assertStatus(200);
    }
}
