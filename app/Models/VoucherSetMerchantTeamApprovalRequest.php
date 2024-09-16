<?php

namespace App\Models;

use App\Events\VoucherSetMerchantTeamApprovalRequest\VoucherSetMerchantTeamApprovalRequestWasCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherSetMerchantTeamApprovalRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

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
}
