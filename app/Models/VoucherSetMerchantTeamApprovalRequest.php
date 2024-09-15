<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherSetMerchantTeamApprovalRequest extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function voucherSet(): BelongsTo
    {
        return $this->belongsTo(VoucherSet::class);
    }

    public function merchantUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_user_id', 'id');
    }
}
