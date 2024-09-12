<?php

namespace Tests\Feature\API\App\MyTeamSearch;

use App\Enums\PersonalAccessTokenAbility;
use App\Models\Team;
use App\Models\Voucher;
use App\Models\VoucherSet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class MyTeamSearchGetTest extends BaseAPITestCase
{
    use RefreshDatabase;

    private string $endPoint = '/my-team-search';

    #[Test]
    public function itFailsIfNotAuthenticated()
    {
        $this->user = $this->createUser();

        $response = $this->get($this->apiRoot . $this->endPoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function itMustHaveSearchQueryOfAtLeastLengthThree()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user,
            abilities: [
                PersonalAccessTokenAbility::MY_TEAM_SEARCH_READ->value,
            ]
        );

        $response = $this->get($this->apiRoot . $this->endPoint . '?query=xy');
        $response->assertStatus(400);

        $responseObject = json_decode($response->getContent(), false);

        self::assertSame('The query field must be at least 3 characters.', $responseObject->meta->message);
    }

    #[Test]
    public function itReturnsRelevantVoucherData()
    {
        $this->user           = $this->createUser();
        $this->user->save();

        Sanctum::actingAs(
            $this->user,
            abilities: [
                PersonalAccessTokenAbility::MY_TEAM_SEARCH_READ->value,
            ]
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
        $this->user->save();

        Sanctum::actingAs(
            $this->user,
            abilities: [
                PersonalAccessTokenAbility::MY_TEAM_SEARCH_READ->value,
            ]
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
    public function itReturnsNotReturnIrrelevantData()
    {
        $this->user           = $this->createUser();
        $this->user->save();

        Sanctum::actingAs(
            $this->user,
            abilities: [
                PersonalAccessTokenAbility::MY_TEAM_SEARCH_READ->value,
            ]
        );

        $otherTeam = Team::factory()->createQuietly();

        $voucher = Voucher::factory()->createQuietly([
            'created_by_team_id' => $otherTeam->id,
            'allocated_to_service_team_id' => $otherTeam->id
        ]);

        $query = Str::substr($voucher->id, 2, 3);

        $response = $this->get($this->apiRoot . $this->endPoint . '?query=' . $query);
        $response->assertStatus(200);

        $responseObject = json_decode($response->getContent(), false);

        self::assertEmpty($responseObject->data->voucherSets);
        self::assertEmpty($responseObject->data->vouchers);
    }

    #[Test]
    public function itDoesNotReturnSingleData()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs(
            $this->user,
            abilities: [
                PersonalAccessTokenAbility::MY_TEAM_SEARCH_READ->value,
            ]
        );

        $response = $this->get($this->apiRoot . $this->endPoint . '/1');
        $response->assertStatus(403);
    }
}
