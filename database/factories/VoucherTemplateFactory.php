<?php

namespace Database\Factories;

use App\Models\VoucherTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VoucherTemplate>
 */
class VoucherTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id'               => fake()->randomDigitNotNull(),
            'created_by_user_id'    => fake()->randomDigitNotNull(),
            'voucher_template_path' => fake()->imageUrl(),
        ];
    }
}
