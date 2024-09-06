<?php

namespace Database\Factories;

use App\Models\VoucherRedemption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VoucherRedemption>
 */
class VoucherRedemptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'redeemed_amount'     => fake()->randomDigitNotNull(),
            'redeemed_by_team_id' => fake()->randomDigitNotNull(),
            'redeemed_by_user_id' => fake()->randomDigitNotNull(),
            'voucher_set_id'      => fake()->uuid(),
            'voucher_id'          => fake()->uuid(),
        ];
    }
}
