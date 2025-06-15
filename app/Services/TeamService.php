<?php

namespace App\Services;

use App\Models\Team;
use Illuminate\Support\Str;

class TeamService
{
    /**
     * Generate uppercase initials for a given team
     *
     * @param Team $team
     */
    public static function generateTeamInitials(Team $team): string
    {
        $words = Str::of($team->name)->trim()->explode(' ');

        return $words->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
            ->implode('');
    }
}
