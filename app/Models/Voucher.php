<?php

namespace App\Models;

use App\Events\Vouchers\VoucherWasCreated;
use App\Events\Vouchers\VoucherWasUpdated;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $keyType   = 'string';
    public $incrementing = false;
    protected $casts     = [
        'expires_at' => 'datetime',
    ];
    protected $dispatchesEvents = [
        'created' => VoucherWasCreated::class,
        'updated' => VoucherWasUpdated::class,
    ];

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

    /**
     * @return BelongsTo
     */
    public function voucherSet(): BelongsTo
    {
        return $this->belongsTo(VoucherSet::class, 'voucher_set_id', 'id');
    }

    public function voucherRedemptions(): HasMany
    {
        return $this->hasMany(VoucherRedemption::class, 'voucher_id', 'id');
    }

    public function voucherBeneficiaryDistributions(): HasMany
    {
        return $this->hasMany(VoucherBeneficiaryDistribution::class, 'voucher_id', 'id');
    }
}
