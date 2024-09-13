<?php

namespace Database\Factories;

use App\Enums\VoucherSetMerchantTeamApprovalRequestStatus;
use App\Models\VoucherSetMerchantTeamApprovalRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VoucherSetMerchantTeamApprovalRequest>
 */
class VoucherSetMerchantTeamApprovalRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'voucher_set_id'   => $this->faker->randomDigitNotNull(),
            'merchant_user_id' => $this->faker->randomDigitNotNull(),
            'approval_status'  => VoucherSetMerchantTeamApprovalRequestStatus::READY->value,
        ];
    }
}
