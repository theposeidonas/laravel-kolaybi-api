<?php

namespace Theposeidonas\Kolaybi\Enums;

enum OrderType: string
{
    case SALE = 'sale_order';
    case PURCHASE = 'purchase_order';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
