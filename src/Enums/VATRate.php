<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum VATRate: int
{
    use HasValues;

    case RATE_0 = 0;
    case RATE_1 = 1;
    case RATE_8 = 8;
    case RATE_10 = 10;
    case RATE_18 = 18;
    case RATE_20 = 20;
}
