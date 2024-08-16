<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'auditable_id',
        'auditable_type',
        'auditable_text',
        'auditable_team_id',
    ];

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'auditable_team_id', 'id');
    }
}
