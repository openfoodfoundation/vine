<?php

namespace App\Models;

use App\Events\Teams\TeamWasCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dispatchesEvents = [
        'created' => TeamWasCreated::class,
    ];

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

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
