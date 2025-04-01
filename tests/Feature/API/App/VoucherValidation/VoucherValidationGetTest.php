<?php

namespace Tests\Feature\API\App\VoucherValidation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherValidationGetTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/voucher-validation';

    /**
     * @return void
     */
    #[Test]
    public function it_fails_to_get_individual_item_every_time()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user
        );
        $response = $this->getJson($this->apiRoot . $this->endPoint . '/1');
        $response->assertStatus(403);
    }

    /**
     * @return void
     */
    #[Test]
    public function it_fails_to_get_all_items_every_time()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->getJson($this->apiRoot . $this->endPoint);
        $response->assertStatus(403);
    }
}
