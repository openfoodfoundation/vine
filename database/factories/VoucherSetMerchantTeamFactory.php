<?php

namespace Database\Factories;

use App\Models\VoucherSetMerchantTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VoucherSetMerchantTeam>
 */
class VoucherSetMerchantTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'voucher_set_id'        => fake()->randomDigitNotNull(),
            'merchant_team_id' => fake()->randomDigitNotNull(),
        ];
    }
}
