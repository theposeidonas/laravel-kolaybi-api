<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum CashFlowDirection: int
{
    use HasValues;

    case IN = 1;
    case OUT = -1;
    case NEUTRAL = 0;
}
