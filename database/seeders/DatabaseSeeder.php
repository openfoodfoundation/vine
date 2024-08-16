<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $usersAndTeams = [
            [
                'team' => [
                    'name' => 'OK200 Team',
                ],
                'users' => [
                    [
                        'name'     => 'Paul Grimes',
                        'email'    => 'paul@ok200.net',
                        'password' => 'paul@ok200.net',
                    ],
                    [
                        'name'     => 'Manami Saito',
                        'email'    => 'manami@ok200.net',
                        'password' => 'manami@ok200.net',
                    ],
                    [
                        'name'     => 'Lyndon Purcell',
                        'email'    => 'lyndon@ok200.net',
                        'password' => 'lyndon@ok200.net',
                    ],
                ],
            ],
            [
                'team' => [
                    'name' => 'Open Food Network',
                ],
                'users' => [
                    [
                        'name'     => 'Maikel Linke',
                        'email'    => 'maikel@openfoodnetwork.org.au',
                        'password' => 'maikel@openfoodnetwork.org.au',
                    ],
                ],
            ],

        ];

        foreach ($usersAndTeams as $userAndTeam) {
            $team = Team::factory()->create(
                [
                    'name' => $userAndTeam['team']['name'],
                ]
            );

            foreach ($userAndTeam['users'] as $user) {
                $user = User::factory()->createQuietly(
                    [
                        'name'     => $user['name'],
                        'email'    => $user['email'],
                        'password' => $user['email'],
                        'is_admin' => 1,
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

    }
}
