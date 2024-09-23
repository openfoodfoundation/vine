<?php

namespace App\Enums;

enum VoucherSetType: string
{
    case FOOD_EQUITY = 'food equity';
    case PROMOTION   = 'promotion';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
