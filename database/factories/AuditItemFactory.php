<?php

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherSet;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AuditItem>
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
        $num = rand(0, 3);

        // If $num == 0, stays as user
        $auditable = User::factory()->createQuietly();

        if ($num === 1) {
            $auditable = Team::factory()->createQuietly();
        }

        if ($num === 2) {
            $auditable = Voucher::factory()->createQuietly();
        }

        if ($num === 3) {
            $auditable = VoucherSet::factory()->createQuietly();
        }

        return [
            'auditable_type' => get_class($auditable),
            'auditable_id'   => $auditable->id,
            'auditable_text' => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'team_id'        => $this->faker->randomDigitNotNull(),
        ];
    }
}
