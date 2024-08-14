<?php

namespace Database\Factories;

use App\Models\TeamServiceTeam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamServiceTeam>
 */
class TeamServiceTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'team_id'         => fake()->randomDigitNotNull(),
            'service_team_id' => fake()->randomDigitNotNull(),
        ];
    }
}
