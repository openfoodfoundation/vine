<?php

namespace Database\Factories;

use App\Enums\VoucherSetType;
use App\Models\VoucherSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VoucherSet>
 */
class VoucherSetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id'                           => $this->faker->uuid(),
            'name'                         => null,
            'created_by_team_id'           => $this->faker->numberBetween(1, 10),
            'allocated_to_service_team_id' => $this->faker->numberBetween(1, 10),
            'created_by_user_id'           => $this->faker->numberBetween(1, 10),
            'total_set_value'              => $this->faker->numberBetween(1, 1000),
            'total_set_value_remaining'    => $this->faker->numberBetween(1, 1000),
            'num_vouchers'                 => $this->faker->numberBetween(1, 100),
            'num_voucher_redemptions'      => $this->faker->numberBetween(1, 10),
            'expires_at'                   => now()->addDays(30),
            'voucher_set_type'             => fake()->randomElement(VoucherSetType::values()),
            'currency_country_id'          => 14,
        ];
    }
}
