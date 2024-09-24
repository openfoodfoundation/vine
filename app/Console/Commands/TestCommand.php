<?php

namespace App\Console\Commands;

use App\Models\Team;
use App\Models\TeamServiceTeam;
use Illuminate\Console\Command;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:go';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test command. Do not run in production.';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $team = Team::factory()->createQuietly();

        TeamServiceTeam::factory()->createQuietly([
            'team_id'         => 1,
            'service_team_id' => $team->id,
        ]);
    }
}
