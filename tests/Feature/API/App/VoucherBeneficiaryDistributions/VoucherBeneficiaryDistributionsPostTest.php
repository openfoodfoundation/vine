<?php

namespace Tests\Feature\API\App\VoucherBeneficiaryDistributions;

use App\Enums\ApiResponse;
use App\Enums\PersonalAccessTokenAbility;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherBeneficiaryDistribution;
use App\Models\VoucherSet;
use Crypt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\API\BaseAPITestCase;

class VoucherBeneficiaryDistributionsPostTest extends BaseAPITestCase
{
    use RefreshDatabase;
    use WithFaker;

    public string $endpoint = '/voucher-beneficiary-distributions';

    #[Test]
    public function authenticationRequired(): void
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->postJson($this->apiRoot . $this->endpoint, []);

        $response->assertStatus(401);
    }

    #[Test]
    public function standardUserWithoutPermissionCannotCreate()
    {
        $this->user = $this->createUser();

        Sanctum::actingAs($this->user, abilities: []);

        $response = $this->postJson($this->apiRoot . $this->endpoint, []);

        $response->assertStatus(401)->assertJson(
            [
                'meta' => [
                    'message' => ApiResponse::RESPONSE_TOKEN_NOT_ALLOWED_TO_DO_THIS->value,
                ],
            ]
        );

    }

    #[Test]
    public function userWithPermissionCanCreateForFirstTime()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_CREATE->value,
        ]);

        $voucherSet = VoucherSet::factory()->create([
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        $voucher = Voucher::factory()->create([
            'allocated_to_service_team_id' => $this->user->current_team_id,
            'voucher_set_id'               => $voucherSet->id,
        ]);

        $beneficiaryEmail = fake()->email();

        $user = User::whereEmail($beneficiaryEmail)->first();
        self::assertNull($user);

        $existingDistributions = VoucherBeneficiaryDistribution::where('voucher_id', $voucher->id)->get();
        self::assertCount(0, $existingDistributions);

        $payload = [
            'voucher_id'        => $voucher->id,
            'beneficiary_email' => $beneficiaryEmail,
        ];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);

        $response->assertStatus(200);

        $existingDistributions = VoucherBeneficiaryDistribution::where('voucher_id', $voucher->id)->get();
        self::assertCount(1, $existingDistributions);
    }

    #[Test]
    public function userWithPermissionAndCorrectDistributionIdCanResend()
    {
        $this->user = $this->createUserWithTeam();

        Sanctum::actingAs($this->user, abilities: [
            PersonalAccessTokenAbility::VOUCHER_BENEFICIARY_DISTRIBUTION_CREATE->value,
        ]);

        $voucherSet = VoucherSet::factory()->create([
            'allocated_to_service_team_id' => $this->user->current_team_id,
        ]);

        $voucher = Voucher::factory()->create([
            'allocated_to_service_team_id' => $this->user->current_team_id,
            'voucher_set_id'               => $voucherSet->id,
        ]);

        $encryptedEmail = Crypt::encrypt(fake()->email);

        $distribution = VoucherBeneficiaryDistribution::factory()->create([
            'voucher_id'                  => $voucher->id,
            'voucher_set_id'              => $voucherSet->id,
            'beneficiary_email_encrypted' => $encryptedEmail,
            'created_by_user_id'          => $this->user->id,
        ]);

        $existingDistributions = VoucherBeneficiaryDistribution::where('voucher_id', $voucher->id)->get();
        $originalCount         = $existingDistributions->count();
        self::assertTrue($originalCount >= 1);

        $payload = [
            'resend_beneficiary_distribution_id' => $distribution->id,
        ];

        $response = $this->postJson($this->apiRoot . $this->endpoint, $payload);
        $response->assertStatus(200);

        $existingDistributions = VoucherBeneficiaryDistribution::where('voucher_id', $voucher->id)->get();
        self::assertCount($originalCount + 1, $existingDistributions);
    }
}
