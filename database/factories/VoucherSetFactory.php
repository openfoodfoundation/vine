<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\VoucherSet>
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
            'created_by_team_id'           => $this->faker->numberBetween(1, 10),
            'allocated_to_service_team_id' => $this->faker->numberBetween(1, 10),
            'created_by_user_id'           => $this->faker->numberBetween(1, 10),
            'total_set_value'              => $this->faker->numberBetween(1, 1000),
            'total_set_value_remaining'    => $this->faker->numberBetween(1, 1000),
            'num_vouchers'                 => $this->faker->numberBetween(1, 100),
            'num_voucher_redemptions'      => $this->faker->numberBetween(1, 10),
        ];
    }
}
