<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamServiceTeam extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }

    public function serviceTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'service_team_id', 'id');
    }
}
