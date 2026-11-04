<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum TransactionDirection: string
{
    use HasValues;

    case IN = 'in';
    case OUT = 'out';
    case ALL = 'all';
}
