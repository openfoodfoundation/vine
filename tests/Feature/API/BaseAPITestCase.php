<?php

namespace Tests\Feature\API;

use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BaseAPITestCase extends TestCase
{
    use DatabaseTransactions;

    public string         $apiRoot = '/api/v1';
    public User|Model     $user;
    public TeamUser|Model $teamUser;
    public Team|Model     $team;

    protected function setUp(): void
    {
        parent::setUp();

        /**
         * Set default referer for all requests. This is necessary, or else
         * we'd need to JWT-sign all requests, which would be a nightmare
         */
        $this->withHeaders(
            [
                'Referer' => env('APP_URL'),
            ]
        );
    }

    public function createUser(): Collection|Model|User
    {
        $this->user = User::factory()->create();

        return $this->user;
    }

    public function createAdminUser(): Collection|Model|User
    {
        $this->user = User::factory()->create([
                                                  'is_admin' => 1,
                                              ]);

        return $this->user;
    }

    public function createUserWithTeam(): Model|Collection|User
    {
        $this->team = Team::factory()
                          ->create();

        $this->user = User::factory()
                          ->create([
                                       'current_team_id' => $this->team->id,
                                       'is_admin'        => 0,
                                   ]);

        $this->teamUser = TeamUser::factory()->createQuietly([
                                                                 'team_id' => $this->team->id,
                                                                 'user_id' => $this->user->id,
                                                             ]);

        return $this->user;
    }
}
