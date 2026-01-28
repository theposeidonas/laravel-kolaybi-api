<?php

namespace Theposeidonas\Kolaybi\Enums;

enum AssociateGroup: string
{
    case EMPLOYEE = 'employee';
    case PARTNER = 'partner';
    case TRADER = 'trader';
    case OTHER = 'other_associate';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
