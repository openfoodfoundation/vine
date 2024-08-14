<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoucherSet extends Model
{
    use HasFactory;
    use HasUuids;

    protected $keyType   = 'string';
    public $incrementing = false;

    /**
     * @return BelongsTo
     */
    public function createdByTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'created_by_team_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function allocatedToServiceTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'allocated_to_service_team_id', 'id');
    }
}
