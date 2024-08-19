<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function teamUsers(): HasMany
    {
        return $this->hasMany(TeamUser::class, 'team_id', 'id');
    }

    public function auditItems(): MorphMany
    {
        return $this->morphMany(AuditItem::class, 'auditable');
    }

    public function teamMerchantTeams(): HasMany
    {
        return $this->hasMany(TeamMerchantTeam::class, 'team_id', 'id');
    }

    public function teamsThisTeamIsMerchantFor(): HasMany
    {
        return $this->hasMany(TeamMerchantTeam::class, 'merchant_team_id', 'id');
    }

    public function teamServiceTeams(): HasMany
    {
        return $this->hasMany(TeamServiceTeam::class, 'team_id', 'id');
    }

    public function teamsThisTeamIsServiceFor(): HasMany
    {
        return $this->hasMany(TeamServiceTeam::class, 'service_team_id', 'id');
    }
}
