<?php

namespace Theposeidonas\Kolaybi\Enums;

enum ProductType: string
{
    case GOOD = 'good';
    case SERVICE = 'service';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
