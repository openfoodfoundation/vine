<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Voucher;
use App\Models\VoucherRedemption;
use App\Models\VoucherSet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $usersAndTeams = [
            [
                'team' => [
                    'name'       => 'OK200 Team',
                    'country_id' => 14, // australia
                ],
                'users' => [
                    [
                        'name'     => 'Paul Grimes',
                        'email'    => 'paul@ok200.net',
                        'password' => 'paul@ok200.net',
                    ],
                    [
                        'name'     => 'Eleanor Bignell',
                        'email'    => 'eleanor@ok200.net',
                        'password' => 'eleanor@ok200.net',
                    ],
                ],
            ],
            [
                'team' => [
                    'name'       => 'Open Food Network',
                    'country_id' => 14, // australia
                ],
                'users' => [
                    [
                        'name'     => 'Maikel Linke',
                        'email'    => 'maikel@openfoodnetwork.org.au',
                        'password' => 'maikel@openfoodnetwork.org.au',
                    ],
                    [
                        'name'     => 'Kirsten Larsen',
                        'email'    => 'kirsten@openfoodnetwork.org.au',
                        'password' => 'kirsten@openfoodnetwork.org.au',
                    ],
                    [
                        'name'     => 'Inca Dunphy',
                        'email'    => 'inca@openfoodnetwork.org.au',
                        'password' => 'inca@openfoodnetwork.org.au',
                    ],
                ],
            ],
        ];

        foreach ($usersAndTeams as $userAndTeam) {
            $team = Team::factory()->create(
                [
                    'name' => $userAndTeam['team']['name'],
                    'country_id' => 14,
                ]
            );

            foreach ($userAndTeam['users'] as $user) {
                $user = User::factory()->create(
                    [
                        'name'            => $user['name'],
                        'email'           => $user['email'],
                        'password'        => $user['email'],
                        'is_admin'        => 1,
                        'current_team_id' => $team->id,
                    ]
                );

                TeamUser::factory()->create(
                    [
                        'team_id' => $team->id,
                        'user_id' => $user->id,
                    ]
                );
            }

        }

        Artisan::call('app:dispatch-collate-system-statistics-job');

    }
}
