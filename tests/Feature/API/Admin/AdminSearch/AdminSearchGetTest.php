<?php

namespace Tests\Feature\API\Admin\AdminSearch;

use App\Models\Voucher;
use App\Models\VoucherSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminSearchGetTest extends BaseAPITestCase
{
    use RefreshDatabase;

    private string $endPoint = '/admin/search';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createAdminUser();

        $response = $this->get($this->apiRoot . $this->endPoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function itMustHaveSearchQueryOfAtLeastLengthThree()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->get($this->apiRoot . $this->endPoint . '?query=xy');
        $response->assertStatus(400);

        $responseObject = json_decode($response->getContent(), false);

        self::assertSame('The query field must be at least 3 characters.', $responseObject->meta->message);
    }

    #[Test]
    public function itReturnsRelevantVoucherData()
    {
        $this->user           = $this->createUserWithTeam();
        $this->user->is_admin = 1;
        $this->user->save();

        Sanctum::actingAs(
            $this->user
        );

        $voucher = Voucher::factory()->createQuietly([
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        $query = Str::substr($voucher->id, 2, 3);

        $response = $this->get($this->apiRoot . $this->endPoint . '?query=' . $query);
        $response->assertStatus(200);

        $responseObject = json_decode($response->getContent(), false);

        foreach ($responseObject->data->vouchers as $v) {
            self::assertStringContainsString($query, $v->id);
        }
    }

    #[Test]
    public function itReturnsRelevantVoucherSetData()
    {
        $this->user           = $this->createUserWithTeam();
        $this->user->is_admin = 1;
        $this->user->save();

        Sanctum::actingAs(
            $this->user
        );

        $voucherSet = VoucherSet::factory()->createQuietly([
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        $query = Str::substr($voucherSet->id, 2, 3);

        $response = $this->get($this->apiRoot . $this->endPoint . '?query=' . $query);
        $response->assertStatus(200);

        $responseObject = json_decode($response->getContent(), false);

        foreach ($responseObject->data->voucherSets as $vs) {
            self::assertStringContainsString($query, $vs->id);
        }
    }

    #[Test]
    public function itDoesNotReturnSingleData()
    {
        $this->user = $this->createAdminUser();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->get($this->apiRoot . $this->endPoint . '/1');
        $response->assertStatus(403);
    }
}
