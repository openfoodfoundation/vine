<?php

namespace Tests\Feature\API\App\VoucherValidation;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
    public function itFailsToGetIndividualItemEveryTime()
    {
        $response = $this->getJson($this->apiRoot . $this->endPoint . '/1');
        $response->assertStatus(403);
    }

    /**
     * @return void
     */
    #[Test]
    public function itFailsToGetAllItemsEveryTime()
    {
        $response = $this->getJson($this->apiRoot . $this->endPoint);
        $response->assertStatus(403);
    }
}
