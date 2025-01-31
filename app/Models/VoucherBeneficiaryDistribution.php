<?php

namespace App\Models;

use App\Events\VoucherBeneficiaryDistributions\VoucherBeneficiaryDistributionCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VoucherBeneficiaryDistribution extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $dispatchesEvents = [
        'created' => VoucherBeneficiaryDistributionCreated::class,
    ];

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function voucherSet(): BelongsTo
    {
        return $this->belongsTo(VoucherSet::class);
    }
}
