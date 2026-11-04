<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum WithholdingType: string
{
    use HasValues;

    case PERCENTAGE = 'PERCENTAGE';
    case NUMERIC = 'NUMERIC';
}
