<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMerchantTeam extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function merchantTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'merchant_team_id', 'id');
    }
}
