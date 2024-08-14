<?php

namespace Database\Factories;

use App\Models\TeamMerchantTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMerchantTeam>
 */
class TeamMerchantTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id'          => fake()->randomDigitNotNull(),
            'merchant_team_id' => fake()->randomDigitNotNull(),
        ];
    }
}
