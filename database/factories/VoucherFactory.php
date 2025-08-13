<?php

namespace Database\Factories;

use App\Services\VoucherService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Voucher>
 */
class VoucherFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $value = rand(500, 1000);

        return [
            'id'                           => $this->faker->uuid(),
            'voucher_set_id'               => $this->faker->uuid(),
            'created_by_team_id'           => $this->faker->numberBetween(1, 10),
            'allocated_to_service_team_id' => $this->faker->numberBetween(1, 10),
            'voucher_value_original'       => $value,
            'voucher_value_remaining'      => $value,
            'voucher_short_code'           => VoucherService::findUniqueShortCodeForVoucher(),
            'expires_at'                   => null,
        ];
    }
}
