<?php

namespace Theposeidonas\Kolaybi\Enums;

use Theposeidonas\Kolaybi\Enums\Concerns\HasValues;

enum ProductType: string
{
    use HasValues;

    case GOOD = 'good';
    case SERVICE = 'service';
}
