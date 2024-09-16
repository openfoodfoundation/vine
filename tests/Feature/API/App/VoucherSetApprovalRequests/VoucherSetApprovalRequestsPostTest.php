<?php
/** @noinspection SpellCheckingInspection */

namespace Tests\Feature\API\App\VoucherSetApprovalRequests;

use App\Enums\PersonalAccessTokenAbility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherSetApprovalRequestsPostTest extends BaseAPITestCase
{
    use RefreshDatabase;

    protected string $endPoint = '/vsmtar';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createUser();

        $response = $this->postJson($this->apiRoot . $this->endPoint);
        $response->assertStatus(401);
    }

    #[Test]
    public function itFailsIfUserDoesNotHaveTheCorrectToken()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user);

        $response = $this->postJson($this->apiRoot . $this->endPoint);
        $response->assertStatus(401);
    }

    #[Test]
    public function itCanNotCreate()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_SET_MERCHANT_TEAM_APPROVAL_REQUESTS_CREATE->value,
        ]);

        $response = $this->postJson($this->apiRoot . $this->endPoint);
        $response->assertStatus(403);
    }
}
