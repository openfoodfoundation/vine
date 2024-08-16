<?php

namespace Database\Factories;

use App\Models\AuditItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditItem>
 */
class AuditItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'auditable_type'    => User::class,
            'auditable_id'      => $this->faker->randomDigitNotNull(),
            'auditable_text'    => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'auditable_team_id' => $this->faker->randomDigitNotNull(),
            'actioning_user_id' => $this->faker->randomDigitNotNull(),
        ];
    }
}
