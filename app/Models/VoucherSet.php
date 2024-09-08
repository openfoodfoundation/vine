<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function voucherSetMerchantTeams(): HasMany
    {
        return $this->hasMany(VoucherSetMerchantTeam::class, 'voucher_set_id', 'id');
    }
}
