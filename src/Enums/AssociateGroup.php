<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum AssociateGroup: string
{
    use HasValues;

    case EMPLOYEE = 'employee';
    case PARTNER = 'partner';
    case TRADER = 'trader';
    case OTHER = 'other_associate';
}
