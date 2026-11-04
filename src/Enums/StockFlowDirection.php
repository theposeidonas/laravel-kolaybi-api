<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum StockFlowDirection: int
{
    use HasValues;

    case IN = 1;
    case OUT = -1;
}
