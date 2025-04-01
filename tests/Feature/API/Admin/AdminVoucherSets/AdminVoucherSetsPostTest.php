<?php

/** @noinspection PhpUndefinedFieldInspection */

namespace Tests\Feature\API\Admin\AdminVoucherSets;

use App\Enums\ApiResponse;
use App\Enums\VoucherSetType;
use App\Models\Team;
use App\Models\TeamMerchantTeam;
use App\Models\VoucherTemplate;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class AdminVoucherSetsPostTest extends BaseAPITestCase
{
    private string $endpoint = '/admin/voucher-sets';

    #[Test]
    public function it_fails_if_not_authenticated()
    {
        $this->user = $this->createUser();

        $response = $this->post($this->apiRoot . $this->endpoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function only_admin_can_access()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs(
            $this->user
        );

        $response = $this->post($this->apiRoot . $this->endpoint);
        $response->assertStatus(302);
    }

    #[Test]
    public function admin_can_save_data()
    {
        $this->user           = $this->createUserWithTeam();
        $this->user->is_admin = 1;
        $this->user->save();

        Sanctum::actingAs(
            $this->user
        );

        $serviceTeam = Team::factory()->create();

        $merchantTeams = Team::factory(5)->create();

        $template = VoucherTemplate::factory()->create([
            'team_id' => $this->user->current_team_id,
        ]);

        foreach ($merchantTeams as $merchantTeam) {
            TeamMerchantTeam::factory()->create(
                [
                    'team_id'          => $serviceTeam->id,
                    'merchant_team_id' => $merchantTeam->id,
                ]
            );
        }

        $payload = [
            'is_test'                      => 1,
            'allocated_to_service_team_id' => $serviceTeam->id,
            'merchant_team_ids'            => $merchantTeams->pluck('id')->toArray(),
            'voucher_template_id'          => $template->id,
            'total_set_value'              => 10,
            'denominations'                => [
                ['number' => 1, 'value' => 10],
            ],
            'voucher_set_type' => VoucherSetType::FOOD_EQUITY->value,
        ];

        $response = $this->post($this->apiRoot . $this->endpoint, $payload);
        $response->assertStatus(200);
    }

    #[Test]
    public function item_is_not_saved_if_merchant_team_does_not_belong_to_service_team()
    {
        $this->user           = $this->createUserWithTeam();
        $this->user->is_admin = 1;
        $this->user->save();

        Sanctum::actingAs(
            $this->user
        );

        $team = Team::factory()
            ->create();

        $serviceTeam = Team::factory()->create();

        $merchantTeams = Team::factory(5)->create();

        $template = VoucherTemplate::factory()->create();

        $payload = [
            'is_test'                      => 1,
            'allocated_to_service_team_id' => $serviceTeam->id,
            'merchant_team_ids'            => $merchantTeams->pluck('id')->toArray(),
            'total_set_value'              => 10,
            'voucher_template_id'          => $template->id,
            'denominations'                => [
                ['number' => 1, 'value' => 10],
            ],
            'voucher_set_type' => VoucherSetType::FOOD_EQUITY->value,
        ];

        $response = $this->post($this->apiRoot . $this->endpoint, $payload);
        $response->assertStatus(400);
        $responseObj = json_decode($response->getContent());

        $this->assertEquals(
            expected: ApiResponse::RESPONSE_INVALID_MERCHANT_TEAM_FOR_SERVICE_TEAM->value,
            actual: $responseObj->meta->message
        );
    }
}
