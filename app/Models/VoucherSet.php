<?php

namespace App\Models;

use App\Events\VoucherSets\VoucherSetWasCreated;
use App\Events\VoucherSets\VoucherSetWasUpdated;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherSet extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $keyType          = 'string';
    public $incrementing        = false;
    protected $dispatchesEvents = [
        'created' => VoucherSetWasCreated::class,
        'updated' => VoucherSetWasUpdated::class,
    ];
    protected $casts = [
        'expires_at' => 'datetime',
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
    public function fundedByTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'funded_by_team_id', 'id');
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

    public function voucherSetMerchantTeamApprovalRequests(): HasMany
    {
        return $this->hasMany(VoucherSetMerchantTeamApprovalRequest::class, 'voucher_set_id', 'id');
    }

    public function voucherSetMerchantTeamApprovalActionedRecord(): BelongsTo
    {
        return $this->belongsTo(VoucherSetMerchantTeamApprovalRequest::class, 'merchant_approval_request_id', 'id');
    }

    public function currencyCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'currency_country_id', 'id');
    }
}
