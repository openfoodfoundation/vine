<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemStatistic extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_users',
        'num_teams',
        'num_voucher_sets',
        'num_vouchers',
        'num_voucher_redemptions',
        'sum_voucher_value_total',
        'sum_voucher_value_redeemed',
        'sum_voucher_value_remaining',
    ];
}
