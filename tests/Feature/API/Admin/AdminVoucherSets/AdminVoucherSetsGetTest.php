<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\Admin\AdminVoucherSets;

use App\Models\VoucherSet;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherSetsGetTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/voucher-sets';

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

        $model = VoucherSet::factory()
            ->create();

        $response = $this->get($this->apiRoot . $this->endpoint);
        $response->assertStatus(200);
        $responseObj = json_decode($response->getContent());
        $this->assertCount(1, $responseObj->data->data);
        $this->assertEquals($responseObj->data->data[0]->id, $model->id);
    }

    #[Test]
    public function itReturnsSingleData()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $model = VoucherSet::factory()
            ->create();

        $response = $this->get($this->apiRoot . $this->endpoint . '/' . $model->id);
        $response->assertStatus(200);
    }
}
