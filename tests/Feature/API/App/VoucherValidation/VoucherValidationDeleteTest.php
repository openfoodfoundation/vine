<?php

namespace Tests\Feature\API\App\VoucherValidation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherValidationDeleteTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/voucher-validation';

    /**
     * @return void
     */
    #[Test]
    public function itFailsToDeleteEveryTime()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->deleteJson($this->apiRoot . $this->endPoint . '/1');
        $response->assertStatus(403);
    }
}
