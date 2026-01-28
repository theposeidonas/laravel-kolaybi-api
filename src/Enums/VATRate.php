<?php

namespace Theposeidonas\Kolaybi\Enums;

enum VATRate: int
{
    case RATE_0 = 0;
    case RATE_1 = 1;
    case RATE_8 = 8;
    case RATE_10 = 10;
    case RATE_18 = 18;
    case RATE_20 = 20;

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
