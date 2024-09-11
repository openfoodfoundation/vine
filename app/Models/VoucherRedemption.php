<?php

namespace App\Models;

use App\Events\VoucherRedemptions\VoucherRedemptionWasCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherRedemption extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dispatchesEvents = [
        'created' => VoucherRedemptionWasCreated::class,
    ];

    public function redeemedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'redeemed_by_user_id', 'id');
    }

    public function redeemedByTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'redeemed_by_team_id', 'id');
    }
}
