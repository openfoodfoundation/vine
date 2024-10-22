<?php

namespace App\Models;

use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasCreated;
use App\Models\Traits\HasRelativeDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherSetMerchantTeamApprovalRequest extends Model
{
    use HasFactory;
    use HasRelativeDates;
    use SoftDeletes;

    protected $appends = [
        'created_at_relative',
        'created_at_date_time'
    ];

    protected $dispatchesEvents = [
        'created' => VoucherSetMerchantTeamApprovalRequestWasCreated::class,
    ];

    public function voucherSet(): BelongsTo
    {
        return $this->belongsTo(VoucherSet::class);
    }

    public function merchantUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_user_id', 'id');
    }

    public function merchantTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'merchant_team_id', 'id');
    }
}
