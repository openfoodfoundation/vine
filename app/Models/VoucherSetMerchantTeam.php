<?php

namespace App\Models;

use App\Events\VoucherSetMerchantTeams\VoucherSetMerchantTeamWasCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherSetMerchantTeam extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dispatchesEvents = [
        'created' => VoucherSetMerchantTeamWasCreated::class,
    ];

    /**
     * @return BelongsTo
     */
    public function merchantTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'merchant_team_id', 'id');
    }
}
